<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Supplier;
use DateTimeImmutable;

final class MedicineValidator
{
    /**
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    public static function validate(array $input): array
    {
        $errors = [];

        $name     = trim((string) ($input['name'] ?? ''));
        $category = trim((string) ($input['category'] ?? ''));
        $stockRaw = trim((string) ($input['stock'] ?? ''));
        $reorderRaw = trim((string) ($input['reorder_level'] ?? ''));
        $expiry   = trim((string) ($input['expiry_date'] ?? ''));
        $supplierRaw = trim((string) ($input['supplier_id'] ?? ''));

        if ($name === '') {
            $errors['name'] = 'Medicine name is required.';
        } elseif (text_length($name) < 2 || text_length($name) > 150) {
            $errors['name'] = 'Medicine name must be between 2 and 150 characters.';
        }

        if ($category === '') {
            $errors['category'] = 'Category is required.';
        } elseif (text_length($category) > 100) {
            $errors['category'] = 'Category cannot exceed 100 characters.';
        }

        $stock = filter_var($stockRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($stockRaw === '' || $stock === false) {
            $errors['stock'] = 'Stock must be a whole number of 0 or more.';
        }

        if ($reorderRaw === '') {
            $reorderRaw = '0';
        }
        $reorder = filter_var($reorderRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($reorder === false) {
            $errors['reorder_level'] = 'Reorder level must be a whole number of 0 or more.';
        }

        $expiryDate = DateTimeImmutable::createFromFormat('!Y-m-d', $expiry);
        if ($expiry === '' || $expiryDate === false || $expiryDate->format('Y-m-d') !== $expiry) {
            $errors['expiry_date'] = 'Enter a valid expiry date.';
        }

        $supplierId = null;
        if ($supplierRaw !== '') {
            $supplierId = filter_var($supplierRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($supplierId === false || Supplier::find((int) $supplierId) === null) {
                $errors['supplier_id'] = 'Choose a valid supplier.';
            }
        }

        $data = [
            ':name'          => $name,
            ':category'      => $category,
            ':stock'         => $stock === false ? 0 : (int) $stock,
            ':reorder_level' => $reorder === false ? 0 : (int) $reorder,
            ':expiry_date'   => $expiryDate instanceof DateTimeImmutable ? $expiryDate->format('Y-m-d') : $expiry,
            ':supplier_id'   => $supplierId === false ? null : $supplierId,
        ];

        return [$data, $errors];
    }
}
