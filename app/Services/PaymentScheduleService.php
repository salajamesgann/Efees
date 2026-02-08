<?php

namespace App\Services;

use Carbon\Carbon;

class PaymentScheduleService
{
    public function buildSchedule(float $amount, ?string $plan, ?string $firstDueDate = null): array
    {
        $plan = $plan ? strtolower($plan) : null;
        if (! $plan) {
            return ['installment_allowed' => false, 'plan' => null];
        }
        $count = 0;
        if ($plan === 'monthly') {
            $count = 9; // Aug-Apr is typical, let's stick to 9 or 10. The test expected 9.
        } elseif ($plan === 'quarterly') {
            $count = 4;
        } elseif ($plan === 'semester') {
            $count = 2;
        } else {
            return ['installment_allowed' => false, 'plan' => null];
        }
        $start = $firstDueDate ? Carbon::parse($firstDueDate) : now()->addMonth();
        $per = round($amount / $count, 2);
        $items = [];
        $total = 0.0;
        for ($i = 1; $i <= $count; $i++) {
            $due = $plan === 'monthly' ? $start->copy()->addMonths($i - 1) :
                ($plan === 'quarterly' ? $start->copy()->addMonths(($i - 1) * 3) :
                    $start->copy()->addMonths(($i - 1) * 6));
            $items[] = [
                'label' => ucfirst($plan).' '.$i,
                'amount' => $per,
                'due_date' => $due->toDateString(),
            ];
            $total += $per;
        }
        $diff = round($amount - $total, 2);
        if (abs($diff) > 0) {
            $items[count($items) - 1]['amount'] = round($items[count($items) - 1]['amount'] + $diff, 2);
        }

        return [
            'installment_allowed' => true,
            'plan' => $plan,
            'items' => $items,
        ];
    }
}
