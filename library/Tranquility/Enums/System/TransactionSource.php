<?php namespace Tranquility\Enums\System;

/**
 * Enumeration of transactions sources that may be used in audit trail fields
 *
 * @package \Tranquility\Enum
 * @author  Andrew Patterson <patto@live.com.au>
 */

class TransactionSource extends \Tranquility\Enums\Enum {
	const UIFrontend = 'audit_transaction_source_frontend_ui';
	const UIBackend  = 'audit_transaction_source_backend_ui';
	const Batch      = 'audit_transaction_source_batch';
	const ApiV1      = 'audit_transaction_source_api_v1';
    const Setup      = 'audit_transaction_source_setup';
}