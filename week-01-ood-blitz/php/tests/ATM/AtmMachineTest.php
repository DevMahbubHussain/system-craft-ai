<?php

declare(strict_types=1);

namespace Tests\ATM;

use App\ATM\AtmMachine;
use App\ATM\Dispenser\Dollar10Dispenser;
use App\ATM\Dispenser\Dollar100Dispenser;
use App\ATM\Dispenser\Dollar20Dispenser;
use App\ATM\Dispenser\Dollar50Dispenser;
use App\ATM\Model\Account;
use App\ATM\Model\Bank;
use App\ATM\Model\TransactionType;
use DomainException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class AtmMachineTest extends TestCase
{
    private const CARD = '4111-2222-3333-4444';
    private const PIN = '1234';

    private Bank $bank;
    private Account $account;

    /** @var array<int, object> */
    private array $links = [];

    protected function setUp(): void
    {
        $this->bank = new Bank('City Bank');
        $this->account = new Account(
            accountNumber: 'ACC-100234',
            cardNumber: self::CARD,
            pin: self::PIN,
            balance: 500.0
        );
        $this->bank->registerAccount($this->account);
    }

    private function buildAtm(
        int $hundreds = 1,
        int $fifties = 1,
        int $twenties = 1,
        int $tens = 1
    ): AtmMachine {
        $this->links = [
            100 => new Dollar100Dispenser($hundreds),
            50 => new Dollar50Dispenser($fifties),
            20 => new Dollar20Dispenser($twenties),
            10 => new Dollar10Dispenser($tens),
        ];

        $this->links[100]->setNext($this->links[50])
            ->setNext($this->links[20])
            ->setNext($this->links[10]);

        return new AtmMachine('ATM-TEST-01', $this->bank, $this->links[100]);
    }

    private function authenticate(AtmMachine $atm): void
    {
        $atm->insertCard(self::CARD);
        $atm->enterPin(self::PIN);
    }

    public function testSuccessfulWithdrawDispensesNotesAndDebitsAccount(): void
    {
        $atm = $this->buildAtm();
        $this->authenticate($atm);

        $notes = $atm->withdrawCash(180);

        $this->assertSame([100 => 1, 50 => 1, 20 => 1, 10 => 1], $notes);
        $this->assertSame(320.0, $this->account->getBalance());
        $this->assertSame(0, $this->links[100]->getAvailableNotes());

        $transactions = $this->account->getTransactions();
        $this->assertCount(1, $transactions);
        $this->assertSame(TransactionType::WITHDRAW, $transactions[0]->getType());
        $this->assertSame(180.0, $transactions[0]->getAmount());
        $this->assertSame(320.0, $transactions[0]->getRemainingBalance());
    }

    public function testFailedDispenseLeavesVaultAndAccountUntouched(): void
    {
        // Vault holds $70 ($50x1 + $20x1). $80 can never be made exactly.
        $atm = $this->buildAtm(hundreds: 0, tens: 0);
        $this->authenticate($atm);

        try {
            $atm->withdrawCash(80);
            $this->fail('Expected RuntimeException for undispensable amount.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('cannot dispense the exact requested amount', $e->getMessage());
        }

        // The greedy walk must not have committed its partial deductions.
        $this->assertSame(1, $this->links[50]->getAvailableNotes());
        $this->assertSame(1, $this->links[20]->getAvailableNotes());
        $this->assertSame(500.0, $this->account->getBalance());

        // The session stays usable and the vault is still fully stocked.
        $notes = $atm->withdrawCash(70);
        $this->assertSame([50 => 1, 20 => 1], $notes);
        $this->assertSame(430.0, $this->account->getBalance());
    }

    public function testAmountNotMatchingDenominationsFailsCleanly(): void
    {
        $atm = $this->buildAtm();
        $this->authenticate($atm);

        try {
            $atm->withdrawCash(25);
            $this->fail('Expected RuntimeException for $25 request.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('cannot dispense the exact requested amount', $e->getMessage());
        }

        $this->assertSame(500.0, $this->account->getBalance());
        $this->assertSame(1, $this->links[100]->getAvailableNotes());
        $this->assertSame(1, $this->links[10]->getAvailableNotes());
    }

    public function testInsufficientAccountBalanceThrows(): void
    {
        $atm = $this->buildAtm(hundreds: 10);
        $this->authenticate($atm);

        $this->account->debit(490.0);

        try {
            $atm->withdrawCash(100);
            $this->fail('Expected RuntimeException for insufficient balance.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Insufficient account balance', $e->getMessage());
        }

        $this->assertSame(10.0, $this->account->getBalance());
        $this->assertSame(10, $this->links[100]->getAvailableNotes());
    }

    public function testWrongPinThrowsAndAllowsRetry(): void
    {
        $atm = $this->buildAtm();
        $atm->insertCard(self::CARD);

        try {
            $atm->enterPin('9999');
            $this->fail('Expected RuntimeException for wrong PIN.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Incorrect PIN', $e->getMessage());
        }

        $atm->enterPin(self::PIN);
        $this->assertSame(500.0, $atm->checkBalance());
    }

    public function testWithdrawalAmountMustBePositive(): void
    {
        $atm = $this->buildAtm();
        $this->authenticate($atm);

        $this->expectException(DomainException::class);
        $atm->withdrawCash(0);
    }

    public function testIdleStateRejectsAllOperations(): void
    {
        $atm = $this->buildAtm();

        try {
            $atm->enterPin(self::PIN);
            $this->fail('Expected DomainException.');
        } catch (DomainException $e) {
            $this->assertSame('Cannot enter PIN. Please insert your card first.', $e->getMessage());
        }

        try {
            $atm->checkBalance();
            $this->fail('Expected DomainException.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('insert your card', $e->getMessage());
        }

        try {
            $atm->withdrawCash(100);
            $this->fail('Expected DomainException.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('insert your card', $e->getMessage());
        }

        try {
            $atm->ejectCard();
            $this->fail('Expected DomainException.');
        } catch (DomainException $e) {
            $this->assertSame('No card to eject.', $e->getMessage());
        }
    }

    public function testCardInsertedStateRejectsBalanceAndWithdraw(): void
    {
        $atm = $this->buildAtm();
        $atm->insertCard(self::CARD);

        try {
            $atm->checkBalance();
            $this->fail('Expected DomainException.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('authenticate with PIN', $e->getMessage());
        }

        try {
            $atm->withdrawCash(100);
            $this->fail('Expected DomainException.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('authenticate with PIN', $e->getMessage());
        }
    }

    public function testEjectCardResetsSessionAndReturnsToIdle(): void
    {
        $atm = $this->buildAtm();
        $this->authenticate($atm);

        $atm->ejectCard();

        $this->assertNull($atm->getInsertedCardNumber());
        $this->assertNull($atm->getCurrentAccount());

        try {
            $atm->checkBalance();
            $this->fail('Expected DomainException after ejection.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('insert your card', $e->getMessage());
        }

        $atm->insertCard(self::CARD);
        $atm->enterPin(self::PIN);
        $this->assertSame(500.0, $atm->checkBalance());
    }

    public function testInsertCardTwiceIsRejected(): void
    {
        $atm = $this->buildAtm();
        $atm->insertCard(self::CARD);

        $this->expectException(DomainException::class);
        $atm->insertCard(self::CARD);
    }
}
