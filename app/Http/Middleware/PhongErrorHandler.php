<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exceptions\PhongException;
use App\Exceptions\SlotException;

class PhongErrorHandler
{
    /**
     * Xử lý request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);

            // Kiểm tra response status codes
            if ($response->isServerError()) {
                Log::error('Server error trong quản lý phòng', [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'status_code' => $response->status()
                ]);
            }

            return $response;

        } catch (PhongException $e) {
            Log::warning('Phong Exception: ' . $e->getMessage(), [
                'url' => $request->fullUrl(),
                'code' => $e->getCode()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], $e->getCode() ?: 422);
            }

            return back()->with('error', $e->getMessage())->withInput();

        } catch (SlotException $e) {
            Log::warning('Slot Exception: ' . $e->getMessage(), [
                'url' => $request->fullUrl(),
                'code' => $e->getCode()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], $e->getCode() ?: 422);
            }

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Model not found trong quản lý phòng', [
                'url' => $request->fullUrl(),
                'model' => $e->getModel()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy dữ liệu yêu cầu'
                ], 404);
            }

            return redirect()->route('phong.index')->with('error', 'Không tìm thấy dữ liệu yêu cầu');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Laravel tự động xử lý validation exception
            throw $e;

        } catch (\Exception $e) {
            Log::error('Unexpected error trong quản lý phòng: ' . $e->getMessage(), [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau!'
                ], 500);
            }

            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại sau!')->withInput();
        }
    }
}



