<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Merchants Export Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f97316;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #f97316;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            flex: 1;
            border: 1px solid #e9ecef;
        }
        .stat-box h3 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #f97316;
        }
        .stat-box p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: #f97316;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-suspended {
            color: #6c757d;
            font-weight: bold;
        }
        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Merchants Export Report</h1>
        <p>Generated on {{ now()->format('F d, Y H:i:s') }}</p>
        <p>Total Merchants: {{ $merchants->count() }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>{{ $merchants->where('status', 'approved')->count() }}</h3>
            <p>Approved</p>
        </div>
        <div class="stat-box">
            <h3>{{ $merchants->where('status', 'pending')->count() }}</h3>
            <p>Pending</p>
        </div>
        <div class="stat-box">
            <h3>{{ $merchants->where('status', 'suspended')->count() }}</h3>
            <p>Suspended</p>
        </div>
        <div class="stat-box">
            <h3>{{ $merchants->where('status', 'rejected')->count() }}</h3>
            <p>Rejected</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Store Name</th>
                <th>Owner Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Products</th>
                <th>Active</th>
                <th>Orders</th>
                <th>Revenue</th>
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($merchants as $merchant)
                <tr>
                    <td>{{ $merchant->store_name ?? 'N/A' }}</td>
                    <td>{{ $merchant->user->name ?? 'N/A' }}</td>
                    <td>{{ $merchant->user->email ?? 'N/A' }}</td>
                    <td>{{ $merchant->phone ?? 'N/A' }}</td>
                    <td>
                        <span class="status-{{ $merchant->status ?? 'unknown' }}">
                            {{ ucfirst($merchant->status ?? 'Unknown') }}
                        </span>
                    </td>
                    <td>{{ $merchant->products_count ?? 0 }}</td>
                    <td>{{ $merchant->active_products_count ?? 0 }}</td>
                    <td>{{ $merchant->orders_count ?? 0 }}</td>
                    <td>৳{{ number_format($merchant->total_revenue ?? 0, 0) }}</td>
                    <td>{{ $merchant->created_at ? $merchant->created_at->format('Y-m-d') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated from the E-Commerce Admin Panel</p>
        <p>For any questions, please contact the system administrator</p>
    </div>
</body>
</html>
