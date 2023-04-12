<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Controllers;

use App\Events\Money\InflowPayment;
use App\Responses\ApiError;
use App\Responses\ApiResponse;
use App\Responses\ApiSuccess;
use App\Role\RoleChecker;
use App\Role\UserRole;
use App\Services\Money\Services\TransactionsService;
use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function getUserByTelegramId($telegramId)
    {
        $user = User::where('telegram_id', $telegramId)->first();
        if (! $user) {
            $user = User::findOrCreate(12);
            $user->update(['telegram_id' => $telegramId]);
        }
        return [
            'user' => $user,
        ];
    }

    public function connectAccounts($telegramId): ApiResponse
    {
        $telegramUser = User::where('telegram_id', $telegramId)->first();

        if($telegramUser->id === Auth::id()) {
            return new ApiSuccess('Already connected', ['user' => Auth::user()]);
        }

        // логика объединения
        // перевод средств

        $roleChecker = new RoleChecker();
        if ($roleChecker->check($telegramUser, UserRole::ROLE_AUTO)) {
            $telegramUser->delete();
        } else {
            $telegramUser->telegram_id = null;
            $telegramUser->save();
        }

        Auth::user()->update(['telegram_id' => $telegramId]);

        return new ApiSuccess('Connected', ['user' => Auth::user()]);
    }

    public function deposit(Request $request, TransactionsService $money): ApiResponse
    {
        $secret = env('API_SECRET', 'abc123');

        if ($request->secret !== $secret) {
            return new ApiError('Bad secret', []);
        }

        $request->validate([
            'amount' => 'required|integer',
            'cur' => 'required|string',
            'paymentId' => 'required|string',
        ]);

//        event(new InflowPayment(Auth::user(), $request->amount,
//            'Пополнение баланса через telegram. Payment Id ' . $request->paymentId));
        $money->create(Auth::user(), Transaction::INFLOW_PAYMENT, $request->amount, $request->cur,
            'Пополнение баланса через telegram. Payment Id ' . $request->paymentId);

        return new ApiSuccess('ok', [
            'paymentId' => $request->paymentId,
            'amount' => $request->amount,
            'balance' => Auth::user()->balance,
        ]);
    }
}
