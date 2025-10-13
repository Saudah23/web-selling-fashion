<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Get users data for JS Grid
     */
    public function data(Request $request)
    {
        $users = User::query()
            ->when($request->filled('name'), function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->when($request->filled('email'), function($query) use ($request) {
                $query->where('email', 'like', '%' . $request->email . '%');
            })
            ->when($request->filled('role'), function($query) use ($request) {
                $query->where('role', $request->role);
            })
            ->when($request->filled('email_verified'), function($query) use ($request) {
                if ($request->email_verified === 'Verified') {
                    $query->whereNotNull('email_verified_at');
                } elseif ($request->email_verified === 'Unverified') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_badge' => $this->getRoleBadge($user->role),
                    'email_verified' => $user->email_verified_at ? 'Verified' : 'Unverified',
                    'created_at' => $user->created_at->format('d/m/Y'),
                ];
            });

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:owner,admin,customer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'email_verified_at' => $request->has('email_verified') ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at->format('d F Y H:i'),
                    'updated_at' => $user->updated_at->format('d F Y H:i'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->id)
                ],
                'role' => 'required|in:owner,admin,customer',
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if ($request->has('email_verified')) {
                $updateData['email_verified_at'] = $request->email_verified ? now() : null;
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deletion of own account
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun Anda sendiri'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role badge HTML
     */
    private function getRoleBadge($role)
    {
        $badges = [
            'owner' => '<span class="badge bg-danger">Owner</span>',
            'admin' => '<span class="badge bg-warning">Admin</span>',
            'customer' => '<span class="badge bg-primary">Customer</span>',
        ];

        return $badges[$role] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}