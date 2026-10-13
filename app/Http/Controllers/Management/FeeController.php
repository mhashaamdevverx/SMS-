<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;

use App\Models\FeeStructure;
use App\Models\FeeCollection;
use App\Models\Classes;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    private function ensureAuthenticated(): void
    {
        if (!session()->has('user_id')) {
            redirect('login')->send();
            exit;
        }
    }

    private function currentUser()
    {
        return User::find(session('user_id'));
    }

    public function index(Request $request)
    {
        $this->ensureAuthenticated();

        $search = trim((string) $request->input('search', ''));
        $selectedClass = trim((string) $request->input('class', ''));
        $selectedStatus = trim((string) $request->input('status', ''));
        $selectedMonth = trim((string) $request->input('month', 'September 2026'));

        $presetOrder = [
            'pre-kg' => 1, 'kg' => 2, 'one' => 3, 'two' => 4, 'three' => 5,
            'four' => 6, 'five' => 7, 'six' => 8, 'seven' => 9, 'eight' => 10,
            'nine' => 11, 'ten' => 12
        ];

        // 1. Fee Structures (Class-wise)
        $feeStructures = FeeStructure::query()
            ->get()
            ->sortBy(function ($fs) use ($presetOrder) {
                return $presetOrder[strtolower(trim($fs->class_name))] ?? 999;
            })
            ->values();

        // 2. Class Options
        $classOptions = Classes::query()
            ->whereNotNull('class_name')
            ->where('class_name', '!=', '')
            ->pluck('class_name')
            ->merge(['Pre-KG', 'KG', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN'])
            ->unique(fn ($c) => strtolower(trim($c)))
            ->sortBy(fn ($c) => $presetOrder[strtolower(trim($c))] ?? 999)
            ->values();

        // 3. Fee Collections Query
        $collectionsQuery = FeeCollection::query();

        if ($selectedMonth !== '') {
            $collectionsQuery->where('month', $selectedMonth);
        }

        if ($selectedClass !== '') {
            $collectionsQuery->whereRaw('LOWER(TRIM(student_class)) = ?', [strtolower($selectedClass)]);
        }

        if ($selectedStatus !== '') {
            $collectionsQuery->where('status', $selectedStatus);
        }

        if ($search !== '') {
            $collectionsQuery->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('std_id', 'like', "%{$search}%")
                  ->orWhere('voucher_no', 'like', "%{$search}%");
            });
        }

        $feeCollections = $collectionsQuery->latest('id')->paginate(10)->withQueryString();

        // 4. Financial Statistics
        $baseStatsQuery = FeeCollection::query();
        if ($selectedMonth !== '') {
            $baseStatsQuery->where('month', $selectedMonth);
        }

        $stats = [
            'total_expected'  => (float) (clone $baseStatsQuery)->sum('net_amount'),
            'total_collected' => (float) (clone $baseStatsQuery)->where('status', 'Paid')->sum('net_amount'),
            'total_pending'   => (float) (clone $baseStatsQuery)->whereIn('status', ['Pending', 'Overdue'])->sum('net_amount'),
            'paid_count'      => (clone $baseStatsQuery)->where('status', 'Paid')->count(),
            'pending_count'   => (clone $baseStatsQuery)->where('status', 'Pending')->count(),
            'overdue_count'   => (clone $baseStatsQuery)->where('status', 'Overdue')->count(),
        ];

        $availableMonths = FeeCollection::select('month')->distinct()->pluck('month')->all();
        if (empty($availableMonths)) {
            $availableMonths = ['September 2026', 'October 2026', 'November 2026'];
        }

        return view('fee-structure', compact(
            'feeStructures',
            'feeCollections',
            'classOptions',
            'stats',
            'search',
            'selectedClass',
            'selectedStatus',
            'selectedMonth',
            'availableMonths'
        ))->with('currentUser', $this->currentUser());
    }

    public function storeClassFee(Request $request)
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'class_name'       => ['required', 'string', 'max:100'],
            'tuition_fee'      => ['required', 'numeric', 'min:0'],
            'admission_fee'    => ['nullable', 'numeric', 'min:0'],
            'exam_fee'         => ['nullable', 'numeric', 'min:0'],
            'test_session_fee' => ['nullable', 'numeric', 'min:0'],
            'lab_sports_fee'   => ['nullable', 'numeric', 'min:0'],
            'other_charges'    => ['nullable', 'numeric', 'min:0'],
        ]);

        $tuition = (float) $validated['tuition_fee'];
        $admission = (float) ($validated['admission_fee'] ?? 0);
        $exam = (float) ($validated['exam_fee'] ?? 0);
        $testSession = (float) ($validated['test_session_fee'] ?? 0);
        $lab = (float) ($validated['lab_sports_fee'] ?? 0);
        $other = (float) ($validated['other_charges'] ?? 0);
        $total = $tuition + $exam + $testSession + $lab + $other;

        FeeStructure::updateOrCreate(
            ['class_name' => trim($validated['class_name'])],
            [
                'user_id'          => session('user_id'),
                'tuition_fee'      => $tuition,
                'admission_fee'    => $admission,
                'exam_fee'         => $exam,
                'test_session_fee' => $testSession,
                'lab_sports_fee'   => $lab,
                'other_charges'    => $other,
                'total_fee'        => $total,
            ]
        );

        return redirect()->route('fee-structure.index')
            ->with('success', "Fee structure for Class {$validated['class_name']} saved successfully!");
    }

    public function updateClassFee(Request $request, FeeStructure $feeStructure)
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'class_name'       => ['required', 'string', 'max:100'],
            'tuition_fee'      => ['required', 'numeric', 'min:0'],
            'admission_fee'    => ['nullable', 'numeric', 'min:0'],
            'exam_fee'         => ['nullable', 'numeric', 'min:0'],
            'test_session_fee' => ['nullable', 'numeric', 'min:0'],
            'lab_sports_fee'   => ['nullable', 'numeric', 'min:0'],
            'other_charges'    => ['nullable', 'numeric', 'min:0'],
        ]);

        $tuition = (float) $validated['tuition_fee'];
        $admission = (float) ($validated['admission_fee'] ?? 0);
        $exam = (float) ($validated['exam_fee'] ?? 0);
        $testSession = (float) ($validated['test_session_fee'] ?? 0);
        $lab = (float) ($validated['lab_sports_fee'] ?? 0);
        $other = (float) ($validated['other_charges'] ?? 0);
        $total = $tuition + $exam + $testSession + $lab + $other;

        $feeStructure->update([
            'class_name'       => trim($validated['class_name']),
            'tuition_fee'      => $tuition,
            'admission_fee'    => $admission,
            'exam_fee'         => $exam,
            'test_session_fee' => $testSession,
            'lab_sports_fee'   => $lab,
            'other_charges'    => $other,
            'total_fee'        => $total,
        ]);

        return redirect()->route('fee-structure.index')
            ->with('success', "Fee structure for Class {$feeStructure->class_name} updated successfully!");
    }

    public function destroyClassFee(FeeStructure $feeStructure)
    {
        $this->ensureAuthenticated();
        $feeStructure->delete();

        return redirect()->route('fee-structure.index')
            ->with('success', 'Class fee structure removed successfully!');
    }

    public function updateFeeStatus(Request $request, FeeCollection $feeCollection)
    {
        $this->ensureAuthenticated();

        $validated = $request->validate([
            'status'         => ['required', 'in:Paid,Pending,Overdue'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'discount'       => ['nullable', 'numeric', 'min:0'],
            'payment_date'   => ['nullable', 'date'],
        ]);

        $discount = (float) ($validated['discount'] ?? $feeCollection->discount);
        $net = max(0, (float) $feeCollection->gross_amount - $discount);

        $feeCollection->update([
            'status'         => $validated['status'],
            'discount'       => $discount,
            'net_amount'     => $net,
            'payment_method' => $validated['payment_method'] ?? ($validated['status'] === 'Paid' ? 'Cash' : null),
            'payment_date'   => $validated['status'] === 'Paid' ? ($validated['payment_date'] ?? date('Y-m-d')) : null,
        ]);

        return redirect()->route('fee-structure.index')
            ->with('success', "Payment status for {$feeCollection->student_name} updated to {$validated['status']}!");
    }

    public function showVoucher(FeeCollection $feeCollection)
    {
        $this->ensureAuthenticated();

        return view('fee-voucher-print', compact('feeCollection'))
            ->with('currentUser', $this->currentUser());
    }
}
