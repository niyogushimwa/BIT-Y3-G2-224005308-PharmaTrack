<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Medicine;

final class AlertValidator
{
    /**
     * @return array{0: array{medicine_id: int, alert_type: string}, 1: array<string, string>}
     */
    public static function validate(array $input): array
    {
        $errors = [];

        $medicineRaw = trim((string) ($input['medicine_id'] ?? ''));
        $type        = trim((string) ($input['alert_type'] ?? ''));

        $medicineId = filter_var($medicineRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($medicineRaw === '' || $medicineId === false) {
            $errors['medicine_id'] = 'Choose a medicine.';
        } elseif (Medicine::find((int) $medicineId) === null) {
            $errors['medicine_id'] = 'That medicine no longer exists.';
        }

        if (!in_array($type, ['low', 'expired'], true)) {
            $errors['alert_type'] = 'Alert type must be low stock or expired.';
        }

        return [
            [
                'medicine_id' => $medicineId === false ? 0 : (int) $medicineId,
                'alert_type'  => $type,
            ],
            $errors,
        ];
    }
}
