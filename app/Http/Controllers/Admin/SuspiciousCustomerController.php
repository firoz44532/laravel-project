<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuspiciousCustomer;
use App\Models\User;
use App\Models\Order;
use App\Http\Requests\Admin\SuspiciousCustomerBanRequest;
use App\Http\Requests\Admin\SuspiciousCustomerNotesUpdateRequest;
use App\Http\Requests\Admin\SuspiciousCustomerBulkActionRequest;
use App\Http\Requests\Admin\SuspiciousCustomerBlockRequest;
use App\Http\Requests\Admin\SuspiciousCustomerStoreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SuspiciousCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = SuspiciousCustomer::query();

        // Filter by risk level
        if ($request->filled('risk_level')) {
            switch ($request->risk_level) {
                case 'high':
                    $query->highRisk();
                    break;
                case 'medium':
                    $query->mediumRisk();
                    break;
                case 'low':
                    $query->where('risk_score', '<', 40);
                    break;
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'banned') {
                $query->banned();
            } elseif ($request->status === 'active') {
                $query->active();
            }
        }

        // Search by email or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suspiciousCustomers = $query->orderBy('risk_score', 'desc')
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(20);

        $stats = [
            'total_flagged' => SuspiciousCustomer::count(),
            'high_risk' => SuspiciousCustomer::highRisk()->count(),
            'medium_risk' => SuspiciousCustomer::mediumRisk()->count(),
            'banned' => SuspiciousCustomer::banned()->count(),
            'total_fake_orders' => SuspiciousCustomer::sum('fake_order_count'),
        ];

        return view('admin.suspicious-customers.index', compact('suspiciousCustomers', 'stats'));
    }

    public function show(SuspiciousCustomer $suspiciousCustomer): View
    {
        // Get related orders if user exists
        $user = User::where('email', $suspiciousCustomer->email)->first();
        $orders = $user ? $user->orders()->latest()->limit(10)->get() : collect();
        
        // Get other suspicious customers with same IP
        $ipMatches = $suspiciousCustomer->ip_address 
            ? SuspiciousCustomer::where('ip_address', $suspiciousCustomer->ip_address)
                               ->where('id', '!=', $suspiciousCustomer->id)
                               ->get()
            : collect();

        return view('admin.suspicious-customers.show', compact('suspiciousCustomer', 'user', 'orders', 'ipMatches'));
    }

    public function ban(SuspiciousCustomerBanRequest $request, SuspiciousCustomer $suspiciousCustomer): RedirectResponse
    {
        $suspiciousCustomer->ban($request->reason, $request->banned_until);
        
        // Add admin notes
        $notes = 'Banned by ' . auth()->user()->name . ' on ' . now()->toDateTimeString() . 
                '. Reason: ' . $request->reason;
        if ($suspiciousCustomer->admin_notes) {
            $notes .= "\n\nPrevious notes:\n" . $suspiciousCustomer->admin_notes;
        }
        $suspiciousCustomer->update(['admin_notes' => $notes]);

        return redirect()->back()
            ->with('success', 'Customer has been banned successfully.');
    }

    public function unban(SuspiciousCustomer $suspiciousCustomer): RedirectResponse
    {
        $suspiciousCustomer->unban();
        
        $notes = 'Unbanned by ' . auth()->user()->name . ' on ' . now()->toDateTimeString();
        if ($suspiciousCustomer->admin_notes) {
            $notes .= "\n\nPrevious notes:\n" . $suspiciousCustomer->admin_notes;
        }
        $suspiciousCustomer->update(['admin_notes' => $notes]);

        return redirect()->back()
            ->with('success', 'Customer has been unbanned successfully.');
    }

    public function updateNotes(SuspiciousCustomerNotesUpdateRequest $request, SuspiciousCustomer $suspiciousCustomer): RedirectResponse
    {
        $suspiciousCustomer->update(['admin_notes' => $request->notes]);

        return redirect()->back()
            ->with('success', 'Admin notes have been updated.');
    }

    public function bulkAction(SuspiciousCustomerBulkActionRequest $request): RedirectResponse
    {
        $customers = SuspiciousCustomer::whereIn('id', $request->customer_ids)->get();

        foreach ($customers as $customer) {
            if ($request->action === 'ban') {
                $customer->ban($request->reason, $request->banned_until);
            } elseif ($request->action === 'unban') {
                $customer->unban();
            }
        }

        $count = $customers->count();
        $message = $request->action === 'ban' 
            ? "{$count} customers have been banned."
            : "{$count} customers have been unbanned.";

        return redirect()->back()->with('success', $message);
    }

    public function analytics(): View
    {
        // Registration trends
        $registrationTrends = SuspiciousCustomer::where('created_at', '>', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Risk level distribution
        $riskDistribution = [
            'high' => SuspiciousCustomer::highRisk()->count(),
            'medium' => SuspiciousCustomer::mediumRisk()->count(),
            'low' => SuspiciousCustomer::where('risk_score', '<', 40)->count(),
        ];

        // Top risk factors
        $allRiskFactors = SuspiciousCustomer::whereNotNull('risk_factors')->get();
        $factorCounts = [];
        foreach ($allRiskFactors as $customer) {
            foreach ($customer->risk_factors as $factor) {
                $factorCounts[$factor] = ($factorCounts[$factor] ?? 0) + 1;
            }
        }
        arsort($factorCounts);
        $topFactors = array_slice($factorCounts, 0, 10, true);

        // Recent high-risk customers
        $recentHighRisk = SuspiciousCustomer::highRisk()
            ->where('created_at', '>', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Fake order trends
        $fakeOrderTrends = SuspiciousCustomer::where('fake_order_count', '>', 0)
            ->selectRaw('fake_order_count, COUNT(*) as count')
            ->groupBy('fake_order_count')
            ->orderBy('fake_order_count', 'desc')
            ->get();

        return view('admin.suspicious-customers.analytics', compact(
            'registrationTrends',
            'riskDistribution',
            'topFactors',
            'recentHighRisk',
            'fakeOrderTrends'
        ));
    }

    public function approve(User $user): RedirectResponse
    {
        // Remove user from suspicious customers list
        SuspiciousCustomer::where('email', $user->email)->delete();
        
        return redirect()->back()
            ->with('success', 'Customer has been approved and removed from suspicious list.');
    }

    public function block(SuspiciousCustomerBlockRequest $request, User $user): RedirectResponse
    {
        // Add to suspicious customers if not already there
        $suspiciousCustomer = SuspiciousCustomer::firstOrCreate(
            ['email' => $user->email],
            [
                'name' => $user->name,
                'phone' => $user->phone,
                'reason' => $request->reason,
                'risk_score' => 75,
                'risk_factors' => ['manual_block'],
                'detection_method' => 'manual',
                'ip_address' => $request->ip(),
            ]
        );

        // Ban the suspicious customer
        $suspiciousCustomer->ban($request->reason);
        
        return redirect()->back()
            ->with('success', 'Customer has been blocked and added to suspicious list.');
    }

    public function create()
    {
        return view('admin.suspicious-customers.create');
    }

    public function store(SuspiciousCustomerStoreRequest $request): RedirectResponse
    {
        $suspiciousCustomer = SuspiciousCustomer::create([
            'email' => $request->email,
            'name' => $request->name,
            'phone' => $request->phone,
            'reason' => $request->reason,
            'risk_score' => $request->risk_score ?? 50,
            'risk_factors' => $request->risk_factors ?? [],
            'detection_method' => $request->detection_method ?? 'manual',
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('admin.suspicious-customers.show', $suspiciousCustomer)
            ->with('success', 'Suspicious customer has been added successfully.');
    }
}
