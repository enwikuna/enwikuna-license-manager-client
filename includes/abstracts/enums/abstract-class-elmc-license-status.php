<?php

/**
 * Class ELMC_License_Status_Abstract
 */
abstract class ELMC_License_Status_Abstract {
	public const IN_STOCK       = 'in_stock';
	public const TRIAL_APPROVED = 'trial_approved';
	public const SOLD           = 'sold';
	public const DELIVERED      = 'delivered';
	public const ACTIVE         = 'active';
	public const INACTIVE       = 'inactive';
	public const LOCKED         = 'locked';

	/**
	 * Return all ENUMS as array
	 *
	 * @return string[]
	 */
	public static function as_array(): array {
		return array(
			'in_stock',
			'trial_approved',
			'sold',
			'delivered',
			'active',
			'inactive',
			'locked',
		);
	}
}
