<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\InvoiceProfile;
use App\Models\InvoiceProfileBank;

class InvoiceProfileController extends Controller
{
    public function index()
    {
        $profiles = InvoiceProfile::with('bankAccounts')->orderBy('name')->get();
        return view('admin.invoice.profile', compact('profiles'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProfile($request);

        $profile = InvoiceProfile::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Профайл амжилттай үүслээ',
            'data' => $profile->load('bankAccounts'),
        ], 201);
    }

    public function update(Request $request, InvoiceProfile $profile)
    {
        $data = $this->validateProfile($request, $profile->id);
        $profile->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Профайл шинэчлэгдлээ',
            'data' => $profile->fresh('bankAccounts'),
        ]);
    }

    public function destroy(InvoiceProfile $profile)
    {
        $profile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Профайл устгагдлаа',
        ]);
    }

    public function storeBank(Request $request, InvoiceProfile $profile)
    {
        $data = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'required|string|max:255',
            'iban' => 'nullable|string|max:255',
            'is_primary' => 'nullable|boolean',
        ])->validate();

        if (!InvoiceProfileBank::where('invoice_profile_id', $profile->id)->exists()) {
            $data['is_primary'] = true;
        }

        if (($data['is_primary'] ?? false) === true) {
            InvoiceProfileBank::where('invoice_profile_id', $profile->id)->update(['is_primary' => false]);
        }

        $bank = $profile->bankAccounts()->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Данс нэмэгдлээ',
            'data' => $bank,
        ], 201);
    }

    public function destroyBank(InvoiceProfile $profile, InvoiceProfileBank $bank)
    {
        if ($bank->invoice_profile_id !== $profile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Данс буруу профайлд харьяалагдсан байна',
            ], 422);
        }

        $bank->delete();

        return response()->json([
            'success' => true,
            'message' => 'Данс устгагдлаа',
        ]);
    }

    private function validateProfile(Request $request, $profileId = null)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'register_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ])->validate();
    }
}

