<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:administrator');
    }

    /**
     * Show the activity log index page.
     */
    public function index(): View
    {
        $users = User::select('uuid', 'nama_lengkap', 'username')->orderBy('nama_lengkap')->get();
        $modules = ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module');
        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('main.activity_log.index', compact('users', 'modules', 'actions'));
    }

    /**
     * Server-side DataTables provider for Activity Logs.
     */
    public function serverside(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user:uuid,nama_lengkap,username');

        // Filters
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_uuid')) {
            $query->where('user_uuid', $request->user_uuid);
        }

        if ($request->filled('tgl_mulai')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->tgl_mulai)->toDateString());
        }

        if ($request->filled('tgl_selesai')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->tgl_selesai)->toDateString());
        }

        // Global search
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'ilike', "%{$search}%")
                  ->orWhere('ip_address', 'ilike', "%{$search}%")
                  ->orWhere('model_id', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nama_lengkap', 'ilike', "%{$search}%")
                         ->orWhere('username', 'ilike', "%{$search}%");
                  });
            });
        }

        $totalData = ActivityLog::count();
        $totalFiltered = $query->count();

        $start = intval($request->input('start', 0));
        $length = intval($request->input('length', 25));

        $logs = $query->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($logs as $index => $log) {
            $badgeColor = match ($log->action) {
                'create' => 'success',
                'update' => 'info',
                'delete' => 'danger',
                'forward' => 'primary',
                'reply' => 'warning',
                'login' => 'secondary',
                'logout' => 'dark',
                default => 'secondary',
            };

            $data[] = [
                'no'          => $start + $index + 1,
                'created_at'  => $log->created_at->format('d/m/Y H:i:s'),
                'user'        => $log->user ? e($log->user->nama_lengkap) . " (" . e($log->user->username) . ")" : 'Sistem / Anonim',
                'action'      => '<span class="badge badge-' . $badgeColor . '">' . e(strtoupper($log->action)) . '</span>',
                'module'      => '<span class="badge badge-light-dark">' . e(strtoupper(str_replace('_', ' ', $log->module))) . '</span>',
                'description' => e($log->description),
                'ip_address'  => e($log->ip_address ?? '-'),
                'details'     => (!empty($log->old_values) || !empty($log->new_values)) 
                    ? '<button class="btn btn-sm btn-outline-info" onclick="viewLogDetail(' . htmlspecialchars(json_encode([
                        'id' => $log->id,
                        'action' => $log->action,
                        'module' => $log->module,
                        'description' => $log->description,
                        'old_values' => $log->old_values,
                        'new_values' => $log->new_values,
                        'user_agent' => $log->user_agent,
                    ]), ENT_QUOTES, 'UTF-8') . ')">Detail</button>' 
                    : '<span class="text-muted">-</span>',
            ];
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }
}
