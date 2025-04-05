<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chatbot;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function __construct() {}

    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'message' => 'required|string',
                'session_id' => 'required|string|max:100',
            ]);

            $customer = Auth::guard('customer')->user();
            $customerId = $customer ? $customer->id : null;

            // ✅ Lưu tin nhắn của người dùng với intent đã xử lý
            $chat = Chatbot::create([
                'customer_id' => $customerId,
                'session_id' => $validatedData['session_id'],
                'sender' => 'customer',
                'message' => $validatedData['message'],
            ]);

            Log::info("Nhận request từ frontend: " . json_encode($request->all()));

            // Gửi yêu cầu đến Flask để lấy phản hồi
            $response = Http::post("http://127.0.0.1:5001/chat", [
                "message" => $validatedData['message'], // ✅ Gửi tin nhắn đã xử lý
            ]);

            Log::info("Phản hồi từ Flask: " . $response->body());

            if ($response->successful()) {
                $botReply = $response->json()['response'] ?? 'Xin lỗi, tôi chưa hiểu câu hỏi của bạn.';

                // Kiểm tra nếu phản hồi trống
                if (!$botReply || trim($botReply) === '') {
                    $botReply = 'Xin lỗi, tôi chưa hiểu câu hỏi của bạn.';
                }

                // ✅ Cập nhật phản hồi cho tin nhắn người dùng
                $chat->update([
                    'response' => $botReply,
                    'intent' => $response->json()['intent']
                ]);

                // ✅ Lưu phản hồi chatbot vào database
                $botChat = Chatbot::create([
                    'customer_id' => $customerId,
                    'session_id' => $validatedData['session_id'],
                    'sender' => 'bot',
                    'message' => $botReply,  // 🔥 Đảm bảo không null
                    'response' => $botReply,
                    'intent' => $response->json()['intent'] ?? 'unknown',
                ]);

                return response()->json([
                    'success' => true,
                    'user_chat' => $chat,
                    'bot_chat' => $botChat
                ]);
            }


            return response()->json(['success' => false, 'message' => 'Lỗi khi gọi chatbot.'], 500);
        } catch (\Exception $e) {
            Log::error('Chatbot create error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra. Vui lòng thử lại!'], 500);
        }
    }
}
