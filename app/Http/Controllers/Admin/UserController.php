<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles')
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->whereIn('status', ['paid', 'delivered', 'shipped', 'processing', 'completed']);
            }], 'total_amount');

        // Search filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanSearch = preg_replace('/\D/', '', $search);

            $query->where(function ($q) use ($search, $cleanSearch) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");

                if (!empty($cleanSearch)) {
                    $q->orWhere('cpf', 'like', "%{$cleanSearch}%")
                      ->orWhere('phone', 'like', "%{$cleanSearch}%");
                }
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'most_orders':
                $query->orderByDesc('orders_count');
                break;
            case 'highest_spent':
                $query->orderByDesc('total_spent');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $users = $query->paginate(12)->withQueryString();

        // Statistics Cards
        $stats = [
            'total_users' => User::count(),
            'total_customers' => User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['Super Admin', 'Admin']))->count(),
            'total_admins' => User::whereHas('roles', fn($q) => $q->whereIn('name', ['Super Admin', 'Admin', 'Manager']))->count(),
            'total_spent' => (float) Order::whereIn('status', ['paid', 'delivered', 'shipped', 'processing', 'completed'])->sum('total_amount'),
            'new_this_month' => User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'stats', 'roles'));
    }

    /**
     * Get full user summary details for instant modal display.
     */
    public function summary(User $user)
    {
        $user->load(['roles', 'addresses']);

        // Orders list
        $orders = $user->orders()
            ->with(['items.product.images'])
            ->latest()
            ->take(15)
            ->get();

        $ordersCount = $user->orders()->count();
        $paidOrdersSum = (float) $user->orders()
            ->whereIn('status', ['paid', 'delivered', 'shipped', 'processing', 'completed'])
            ->sum('total_amount');

        $paidOrdersCount = $user->orders()
            ->whereIn('status', ['paid', 'delivered', 'shipped', 'processing', 'completed'])
            ->count();

        $averageTicket = $paidOrdersCount > 0 ? ($paidOrdersSum / $paidOrdersCount) : 0;

        // Customer classification & segmentation
        $segment = 'Novo Cliente';
        $segmentColor = 'slate';
        if ($paidOrdersSum >= 1000 || $paidOrdersCount >= 3) {
            $segment = 'Cliente VIP ⭐';
            $segmentColor = 'amber';
        } elseif ($paidOrdersCount >= 2) {
            $segment = 'Cliente Recorrente 💎';
            $segmentColor = 'emerald';
        } elseif ($paidOrdersCount === 1) {
            $segment = 'Primeira Compra 👟';
            $segmentColor = 'blue';
        } elseif ($ordersCount > 0) {
            $segment = 'Aguardando Pagamento';
            $segmentColor = 'purple';
        }

        // Phone and CPF formatting
        $cleanPhone = preg_replace('/\D/', '', $user->phone ?? '');
        $formattedPhone = $user->phone;
        if (strlen($cleanPhone) === 11) {
            $formattedPhone = sprintf('(%s) %s-%s', substr($cleanPhone, 0, 2), substr($cleanPhone, 2, 5), substr($cleanPhone, 7));
        } elseif (strlen($cleanPhone) === 10) {
            $formattedPhone = sprintf('(%s) %s-%s', substr($cleanPhone, 0, 2), substr($cleanPhone, 2, 4), substr($cleanPhone, 6));
        }

        $cleanCpf = preg_replace('/\D/', '', $user->cpf ?? '');
        $formattedCpf = $user->cpf;
        if (strlen($cleanCpf) === 11) {
            $formattedCpf = sprintf('%s.%s.%s-%s', substr($cleanCpf, 0, 3), substr($cleanCpf, 3, 3), substr($cleanCpf, 6, 3), substr($cleanCpf, 9));
        }

        $whatsPhone = $cleanPhone;
        if ($whatsPhone && !str_starts_with($whatsPhone, '55')) {
            $whatsPhone = '55' . $whatsPhone;
        }
        $whatsUrl = $whatsPhone ? "https://wa.me/{$whatsPhone}?text=" . urlencode("Olá {$user->name}, tudo bem? Aqui é da equipe Denner Tênis! Como podemos te ajudar?") : null;

        // Last activity / login
        $lastLogin = null;
        $lastLoginDiff = null;

        if ($user->last_login_at) {
            $lastLogin = $user->last_login_at->format('d/m/Y \à\s H:i');
            $lastLoginDiff = $user->last_login_at->diffForHumans();
        } else {
            // Check session table fallback
            $sessionActivity = DB::table('sessions')
                ->where('user_id', $user->id)
                ->max('last_activity');
            if ($sessionActivity) {
                $dt = Carbon::createFromTimestamp($sessionActivity);
                $lastLogin = $dt->format('d/m/Y \à\s H:i');
                $lastLoginDiff = $dt->diffForHumans();
            } else {
                $lastLogin = 'Sem login registrado';
                $lastLoginDiff = 'Nunca acessou';
            }
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'cpf' => $formattedCpf ?: 'Não informado',
                'raw_cpf' => $cleanCpf,
                'phone' => $formattedPhone ?: 'Não informado',
                'raw_phone' => $cleanPhone,
                'clean_phone' => $cleanPhone,
                'whats_url' => $whatsUrl,
                'created_at' => $user->created_at->format('d/m/Y \à\s H:i'),
                'created_at_diff' => $user->created_at->diffForHumans(),
                'last_login' => $lastLogin,
                'last_login_diff' => $lastLoginDiff,
                'last_login_ip' => $user->last_login_ip ?: '---',
                'roles' => $user->roles->pluck('name')->toArray(),
                'segment' => $segment,
                'segment_color' => $segmentColor,
                'asaas_customer_id' => $user->asaas_customer_id ?: 'Não integrado',
                'edit_url' => route('admin.users.edit', $user),
            ],
            'stats' => [
                'orders_count' => $ordersCount,
                'paid_orders_count' => $paidOrdersCount,
                'pending_orders_count' => $ordersCount - $paidOrdersCount,
                'total_spent' => number_format($paidOrdersSum, 2, ',', '.'),
                'total_spent_raw' => $paidOrdersSum,
                'average_ticket' => number_format($averageTicket, 2, ',', '.'),
            ],
            'addresses' => $user->addresses->map(function ($addr) {
                $full = "{$addr->street}, {$addr->number}" . ($addr->complement ? " ({$addr->complement})" : '') . " - {$addr->neighborhood}, {$addr->city}/{$addr->state} - CEP {$addr->zip_code}";
                return [
                    'id' => $addr->id,
                    'label' => $addr->label ?: 'Principal',
                    'recipient_name' => $addr->recipient_name,
                    'zip_code' => $addr->zip_code,
                    'street' => $addr->street,
                    'number' => $addr->number,
                    'complement' => $addr->complement,
                    'neighborhood' => $addr->neighborhood,
                    'city' => $addr->city,
                    'state' => $addr->state,
                    'is_default' => (bool)$addr->is_default,
                    'full_address' => $full,
                    'maps_url' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode($full),
                ];
            }),
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'created_at' => $order->created_at->format('d/m/Y \à\s H:i'),
                    'created_at_diff' => $order->created_at->diffForHumans(),
                    'status' => $order->status,
                    'status_label' => match($order->status) {
                        'paid' => 'Pago',
                        'delivered' => 'Entregue',
                        'shipped' => 'Em trânsito',
                        'processing' => 'Preparando',
                        'pending' => 'Aguardando Pagamento',
                        'cancelled' => 'Cancelado',
                        default => ucfirst($order->status),
                    },
                    'status_color' => match($order->status) {
                        'paid', 'delivered' => 'emerald',
                        'shipped', 'processing' => 'blue',
                        'pending' => 'amber',
                        'cancelled' => 'red',
                        default => 'slate',
                    },
                    'total_amount' => number_format((float)$order->total_amount, 2, ',', '.'),
                    'subtotal' => number_format((float)$order->subtotal, 2, ',', '.'),
                    'shipping_amount' => number_format((float)$order->shipping_amount, 2, ',', '.'),
                    'shipping_modalidade' => $order->shipping_modalidade ?: 'Retirada / Padrão',
                    'tracking_code' => $order->tracking_code ?: null,
                    'payment_method' => strtoupper($order->payment_method ?? 'PIX'),
                    'items_count' => $order->items->sum('quantity'),
                    'items' => $order->items->map(function ($item) {
                        $img = $item->product?->images?->where('is_main', true)->first()?->path 
                            ?? $item->product?->images?->first()?->path 
                            ?? '';
                        return [
                            'name' => $item->product?->name ?? 'Tênis',
                            'quantity' => $item->quantity,
                            'price' => number_format((float)$item->price, 2, ',', '.'),
                            'size' => $item->size,
                            'color_name' => $item->color_name,
                            'image' => $img ? (str_starts_with($img, 'http') ? $img : asset('storage/' . $img)) : '',
                        ];
                    }),
                    'view_url' => route('admin.orders.show', $order),
                ];
            }),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'nullable|string|max:14|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'cpf' => 'nullable|string|max:14|unique:users,cpf,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);
        $user->syncRoles($request->roles);

        return redirect()->route('admin.users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Você não pode excluir a si mesmo!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
