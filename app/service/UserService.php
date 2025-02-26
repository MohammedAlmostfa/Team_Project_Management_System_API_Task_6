<?php

namespace App\Service;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Display all users.
     *
     * @return array
     */
    public function showUsers()
    {
        try {
            $data = User::byRole('user')->get(['id', 'name']);
            return [
                'message' => 'Users data retrieved successfully',
                'status' => 200,
                'data' => $data,
            ];
        } catch (Exception $e) {
            Log::error('Error in show all users: ' . $e->getMessage());
            return [
                'message' => 'An error occurred while retrieving users',
                'status' => 500,
                'data' => 'No data available',
            ];
        }
    }

    /**
     * Create a new user.
     *
     * @param array $credentials
     * @return array
     */
    public function createUser($credentials)
    {
        try {
            $user = User::create($credentials);
            return [
                'message' => 'User created successfully',
                'status' => 200,
                'data' => [
                    'name' => $credentials['name'],
                    'email' => $credentials['email'],
                ],
            ];
        } catch (Exception $e) {
            Log::error('Error in creating user: ' . $e->getMessage());
            return [
                'message' => 'An error occurred while creating the user',
                'status' => 500,
                'data' => 'No data available',
            ];
        }
    }

    /**
     * Update the specified user.
     *
     * @param array $data
     * @param int $id
     * @return array
     */
    public function updateUser($data, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return [
                    'message' => 'User not found',
                    'status' => 404,
                    'data' => 'No data available',
                ];
            } else {
                $user->update([
                    'name' => $data['name'] ?? $user->name,
                    'email' => $data['email'] ?? $user->email,
                    'password' => $data['password'] ?? $user->password,
                    'role' => $data['role'] ?? $user->role,
                ]);
                return [
                    'message' => 'User updated successfully',
                    'status' => 200,
                    'data' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in updating user: ' . $e->getMessage());
            return [
                'message' => 'An error occurred while updating the user',
                'status' => 500,
                'data' => 'No data available',
            ];
        }
    }

    /**
     * Delete the specified user.
     *
     * @param int $id
     * @return array
     */
    public function deletUser($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return [
                    'message' => 'User not found',
                    'status' => 404,
                ];
            } else {
                $user->delete();
                return [
                    'message' => 'User deleted successfully',
                    'status' => 200,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in deleting user: ' . $e->getMessage());
            return [
                'message' => 'An error occurred while deleting the user',
                'status' => 500,
            ];
        }
    }

    /**
     * Restore the specified user.
     *
     * @param int $id
     * @return array
     */
    public function returnUser($id)
    {
        try {
            $user = User::withTrashed()->find($id);

            if ($user) {
                if ($user->deleted_at != "") {
                    $user->restore();
                    return [
                        'message' => 'User restored successfully',
                        'data' => $user,
                        'status' => 200,
                    ];
                } else {
                    return [
                        'message' => 'User is not deleted',
                        'data' => $user,
                        'status' => 200,
                    ];
                }
            } else {
                return [
                    'message' => 'User not found',
                    'data' => 'No data available',
                    'status' => 404,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in restoring user: ' . $e->getMessage());
            return [
                'message' => 'An error occurred while restoring the user',
                'status' => 500,
                'data' => 'No data available',
            ];
        }
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return array
     */
    public function showUser($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return [
                    'message' => 'User not found',
                    'status' => 404,
                    'data' => 'No data available',
                ];
            } else {
                return [
                    'message' => 'User data retrieved successfully',
                    'data' => $user,
                    'status' => 200,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error in showing user: ' . $e->getMessage());
            return [
                'message' => 'An error occurred while retrieving the user',
                'status' => 500,
                'data' => 'No data available',
            ];
        }
    }

    /**
     * Display a list of soft-deleted users.
     *
     * @return array
     */
    public function showDeletedUsers()
    {
        try {
            // Retrieve only soft-deleted users
            $deletedUsers = User::onlyTrashed()->get();

            return [
                'message' => 'Soft-deleted users retrieved successfully',
                'data' => $deletedUsers,
                'status' => 200,
            ];
        } catch (Exception $e) {
            Log::error('Error in retrieving soft-deleted users: ' . $e->getMessage());
            return [
                'message' => 'An error occurred while retrieving soft-deleted users',
                'data' => [],
                'status' => 500,
            ];
        }
    }


}
