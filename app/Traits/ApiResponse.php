<?php

namespace App\Traits;

trait ApiResponse
{
    protected function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(string $message = 'Error', int $code = 400, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    protected function notFound(string $message = 'Data tidak ditemukan')
    {
        return $this->error($message, 404);
    }

    protected function unauthorized(string $message = 'Unauthenticated')
    {
        return $this->error($message, 401);
    }

    protected function forbidden(string $message = 'Anda tidak memiliki akses')
    {
        return $this->error($message, 403);
    }

    protected function validationError($errors, string $message = 'Data yang dikirim tidak valid')
    {
        return $this->error($message, 422, $errors);
    }

    protected function serverError(string $message = 'Terjadi kesalahan pada server')
    {
        return $this->error($message, 500);
    }
}