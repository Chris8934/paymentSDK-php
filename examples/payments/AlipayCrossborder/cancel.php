<?php
// # Refund a transaction

// To refund a transaction, a cancel request with the parent transaction is sent.

// ## Required objects

// To include the necessary files, we use the composer for PSR-4 autoloading.
require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../inc/common.php';
require __DIR__ . '/../../configuration/globalconfig.php';
// Header design included
require __DIR__ . '/../../inc/header.php';

use Wirecard\PaymentSdk\Response\FailureResponse;
use Wirecard\PaymentSdk\Response\SuccessResponse;
use Wirecard\PaymentSdk\Transaction\AlipayCrossborderTransaction;
use Wirecard\PaymentSdk\TransactionService;
use Wirecard\PaymentSdk\Entity\Amount;

if (!isset($_POST['parentTransactionId'])) {
    ?>
    <form action="cancel.php" method="post">
        <div class="form-group">
            <label for="parentTransactionId">Transaction ID to be refunded:</label><br>
            <input id="parentTransactionId" name="parentTransactionId" class="form-control" />
        </div>
        <button type="submit" class="btn btn-primary">Refund</button>
    </form>
    <?php
} else {
// ## Transaction
    $transaction = new AlipayCrossborderTransaction();
    $transaction->setParentTransactionId($_POST['parentTransactionId']);
    $amount = new Amount(1.59, 'EUR');
    $transaction->setAmount($amount);

// ### Transaction Service

// The _TransactionService_ is used to generate the request data needed for the generation of the UI.
    $transactionService = new TransactionService($config);
    $response = $transactionService->cancel($transaction);


// ## Response handling

// The response from the service can be used for disambiguation.
// In case of a successful transaction, a `SuccessResponse` object is returned.
    if ($response instanceof SuccessResponse) {
        echo 'Payment successfully cancelled.<br>';
        echo getTransactionLink($baseUrl, $response);
// In case of a failed transaction, a `FailureResponse` object is returned.
    } elseif ($response instanceof FailureResponse) {
        // In our example we iterate over all errors and echo them out.
        // You should display them as error, warning or information based on the given severity.
        foreach ($response->getStatusCollection() as $status) {
            /**
             * @var Wirecard\PaymentSdk\Entity\Status $status
             */
            $severity = ucfirst($status->getSeverity());
            $code = $status->getCode();
            $description = $status->getDescription();
            echo sprintf('%s with code %s and message "%s" occurred.<br>', $severity, $code, $description);
        }
    }
}
// Footer design
require __DIR__ . '/../../inc/footer.php';
