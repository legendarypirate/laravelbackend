<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceProfile;
use App\Models\InvoiceProfileBank;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Display the invoice index page with data
     */
   public function index(Request $request)
{
    // Get invoices with pagination and filters
    $query = Invoice::query();
    
    // Apply filters from request
    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    }
    
    if ($request->has('start_date') && $request->start_date != '') {
        $query->where('invoice_date', '>=', $request->start_date);
    }
    
    if ($request->has('end_date') && $request->end_date != '') {
        $query->where('invoice_date', '<=', $request->end_date);
    }
    
    // Search
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('invoice_number', 'like', "%$search%")
              ->orWhere('customer_name', 'like', "%$search%")
              ->orWhere('customer_email', 'like', "%$search%");
        });
    }
    
    // Order by latest
    $query->orderBy('created_at', 'desc');
    
    // Get statistics
    $stats = [
        'total_invoices' => Invoice::count(),
        'total_amount' => Invoice::sum('total'),
        'pending_invoices' => Invoice::where('status', 'pending')->count(),
        'pending_amount' => Invoice::where('status', 'pending')->sum('total'),
        'paid_invoices' => Invoice::where('status', 'paid')->count(),
        'paid_amount' => Invoice::where('status', 'paid')->sum('total'),
        'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
        'overdue_amount' => Invoice::where('status', 'overdue')->sum('total'),
    ];
    
    // Paginate results
    $perPage = $request->per_page ?? 15;
    $invoices = $query->paginate($perPage);
    
    // Decode JSON items for each invoice and format dates
    $invoices->getCollection()->transform(function ($invoice) {
        // Check if items is already an array (due to model cast) before decoding
        if (is_string($invoice->items)) {
            $invoice->items = json_decode($invoice->items, true);
        } elseif (!is_array($invoice->items)) {
            $invoice->items = [];
        }

        // Ensure totals are consistent across items
        if (is_array($invoice->items) && count($invoice->items)) {
            $totals = Invoice::calculateTotals($invoice->items);
            $invoice->subtotal = $totals['subtotal'];
            $invoice->tax = $invoice->tax ?? $totals['tax'];
            $invoice->total = $invoice->total ?? ($invoice->subtotal + $invoice->tax);
        }

        $invoice->formatted_total = number_format($invoice->total, 2);
        
        // Format dates to YYYY-MM-DD
        if ($invoice->invoice_date) {
            $invoice->formatted_date = $this->formatDate($invoice->invoice_date);
        } else {
            $invoice->formatted_date = '';
        }
        
        if ($invoice->due_date) {
            $invoice->formatted_due_date = $this->formatDate($invoice->due_date);
        } else {
            $invoice->formatted_due_date = '';
        }
        
        // Format payment dates if they exist
        if ($invoice->payment_date) {
            $invoice->formatted_payment_date = $this->formatDate($invoice->payment_date);
        }
        
        if ($invoice->paid_at) {
            $invoice->formatted_paid_at = $this->formatDate($invoice->paid_at);
        }
        
        return $invoice;
    });
    
    // Get status counts for filter
    $statusCounts = [
        'all' => Invoice::count(),
        'pending' => Invoice::where('status', 'pending')->count(),
        'paid' => Invoice::where('status', 'paid')->count(),
        'overdue' => Invoice::where('status', 'overdue')->count(),
        'cancelled' => Invoice::where('status', 'cancelled')->count(),
    ];
    
    $profiles = InvoiceProfile::with('bankAccounts')->orderBy('name')->get();
    
    return view('admin.invoice.index', compact('invoices', 'stats', 'statusCounts', 'profiles'));
}

/**
 * Format date to YYYY-MM-DD format
 * Handles both Carbon objects and string dates
 */
private function formatDate($date)
{
    if (!$date) {
        return '';
    }
    
    // If it's a Carbon instance
    if ($date instanceof \Carbon\Carbon) {
        return $date->format('Y-m-d');
    }
    
    // If it's a string with ISO format
    if (is_string($date) && str_contains($date, 'T')) {
        // Extract just the date part from ISO format
        return substr($date, 0, 10);
    }
    
    // If it's already in YYYY-MM-DD format
    if (is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return $date;
    }
    
    // Try to parse as date
    try {
        $carbonDate = \Carbon\Carbon::parse($date);
        return $carbonDate->format('Y-m-d');
    } catch (\Exception $e) {
        // Return original if can't parse
        return $date;
    }
}

    /**
     * Get list of invoices for AJAX/DataTables
     */
    public function list(Request $request)
    {
        // This is for AJAX DataTables if needed
        $query = Invoice::query();
        
        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('invoice_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('invoice_date', '<=', $request->end_date);
        }
        
        // For DataTables server-side processing
        if ($request->ajax()) {
            return datatables()->eloquent($query)
                ->addColumn('DT_RowIndex', function($invoice) {
                    return '';
                })
                ->addColumn('actions', function($invoice) {
                    return view('admin.invoice.partials.actions', compact('invoice'))->render();
                })
                ->editColumn('total', function($invoice) {
                    return number_format($invoice->total, 2);
                })
                ->editColumn('status', function($invoice) {
                    $statusText = '';
                    $statusClass = '';
                    
                    switch($invoice->status) {
                        case 'pending':
                            $statusClass = 'warning';
                            $statusText = 'Хүлээгдэж буй';
                            break;
                        case 'paid':
                            $statusClass = 'success';
                            $statusText = 'Төлөгдсөн';
                            break;
                        case 'overdue':
                            $statusClass = 'danger';
                            $statusText = 'Хугацаа хэтэрсэн';
                            break;
                        case 'cancelled':
                            $statusClass = 'secondary';
                            $statusText = 'Цуцлагдсан';
                            break;
                    }
                    
                    return '<span class="badge badge-' . $statusClass . '">' . $statusText . '</span>';
                })
                ->editColumn('invoice_date', function($invoice) {
                    return $invoice->invoice_date->format('Y-m-d');
                })
                ->editColumn('due_date', function($invoice) {
                    return $invoice->due_date->format('Y-m-d');
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }
        
        return response()->json(['error' => 'Not an AJAX request'], 400);
    }

    /**
     * Create a new invoice
     */
    public function create(Request $request)
    {
        // Your existing create method
        $validator = Validator::make($request->all(), [
            'invoice_number' => 'required|string|max:255|unique:invoices',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,paid,overdue,cancelled',
            'issuer_profile_id' => 'nullable|exists:invoice_profiles,id',
            'issuer_bank_account_id' => 'nullable|exists:invoice_profile_banks,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $profile = null;
            $bankAccount = null;

            if ($request->issuer_profile_id) {
                $profile = InvoiceProfile::with('bankAccounts')->find($request->issuer_profile_id);
            }

            if ($request->issuer_bank_account_id) {
                $bankAccount = InvoiceProfileBank::where('id', $request->issuer_bank_account_id)
                    ->where('invoice_profile_id', $request->issuer_profile_id)
                    ->first();

                if (!$bankAccount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Сонгосон данс профайлд таарахгүй байна'
                    ], 422);
                }
            }

            // Always calculate totals from items to avoid incorrect subtotals
            // Treat price as VAT-inclusive
            $taxPercent = $request->has('tax_percent') ? (float) $request->tax_percent : 10;
            $totals = Invoice::calculateTotals($request->items, $taxPercent);
            $tax = $request->has('tax') ? (float) $request->tax : $totals['tax'];
            $subtotal = $totals['subtotal'];
            $total = round($subtotal + $tax, 2);

            $invoice = Invoice::create([
                'invoice_number' => $request->invoice_number,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => $request->status ?? 'pending',
                'items' => json_encode($request->items),
                'notes' => $request->notes,
                'issuer_profile_id' => $profile?->id,
                'issuer_bank_account_id' => $bankAccount?->id,
                'issuer_profile_snapshot' => $profile ? $profile->only([
                    'id', 'name', 'register_number', 'email', 'phone', 'address'
                ]) : null,
                'issuer_bank_snapshot' => $bankAccount ? $bankAccount->only([
                    'id', 'bank_name', 'account_name', 'account_number', 'iban', 'is_primary'
                ]) : null,
            ]);

            // Generate PDF and upload to Cloudinary
            try {
                $pdfUrl = $this->generateAndUploadPdf($invoice);
                if ($pdfUrl) {
                    $invoice->update(['pdf_url' => $pdfUrl]);
                }
            } catch (\Exception $e) {
                \Log::error('PDF generation/upload error: ' . $e->getMessage());
                // Continue even if PDF generation fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoice
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show invoice details
     */
    /**
 * Show invoice details
 */
public function show($id)
{
    try {
        $invoice = Invoice::withTrashed()->find($id);
        
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Нэхэмжлэл олдсонгүй'
            ], 404);
        }
        
        // JSON items-ийг decode хийх
        // Check if items is already an array (due to model cast) before decoding
        if (is_string($invoice->items)) {
            $invoice->items = json_decode($invoice->items, true);
        } elseif (!is_array($invoice->items)) {
            $invoice->items = [];
        }
        // Recalculate subtotal/tax/total to avoid stale or partially saved values
        if (is_array($invoice->items) && count($invoice->items)) {
            $totals = Invoice::calculateTotals($invoice->items);
            $invoice->subtotal = $totals['subtotal'];
            $invoice->tax = $invoice->tax ?? $totals['tax'];
            $invoice->total = $invoice->total ?? ($invoice->subtotal + $invoice->tax);
        }
        
        // Огноог форматлах
        $invoice->invoice_date = $invoice->invoice_date->format('Y-m-d');
        $invoice->due_date = $invoice->due_date->format('Y-m-d');
        
        if ($invoice->payment_date) {
            $invoice->payment_date = $invoice->payment_date->format('Y-m-d');
        }
        
        if ($invoice->paid_at) {
            $invoice->paid_at = $invoice->paid_at->format('Y-m-d H:i:s');
        }

        if (!$invoice->issuer_profile_snapshot && $invoice->issuerProfile) {
            $invoice->issuer_profile_snapshot = $invoice->issuerProfile->only([
                'id', 'name', 'register_number', 'email', 'phone', 'address'
            ]);
        }

        if (!$invoice->issuer_bank_snapshot && $invoice->issuerBankAccount) {
            $invoice->issuer_bank_snapshot = $invoice->issuerBankAccount->only([
                'id', 'bank_name', 'account_name', 'account_number', 'iban', 'is_primary'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Invoice show error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Алдаа гарлаа: ' . $e->getMessage(),
            'error' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Resolve an invoice
     */
    public function resolve(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'status' => 'required|in:paid,resolved,overdue,cancelled',
            'payment_method' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $invoice = Invoice::find($request->invoice_id);
            
            $updateData = [
                'status' => $request->status
            ];
            
            if ($request->status === 'paid') {
                $updateData['payment_method'] = $request->payment_method;
                $updateData['payment_date'] = $request->payment_date ?? now();
                $updateData['paid_at'] = now();
            }
            
            if ($request->notes) {
                $updateData['resolution_notes'] = $request->notes;
            }
            
            $invoice->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice resolved successfully',
                'data' => $invoice
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF and upload to Cloudinary
     */
    private function generateAndUploadPdf($invoice)
    {
        try {
            // Generate PDF
            $pdf = Pdf::loadView('admin.invoice.pdf', ['invoice' => $invoice]);
            $pdfContent = $pdf->output();
            
            // Save PDF temporarily
            $tempPath = storage_path('app/temp/invoice_' . $invoice->invoice_number . '_' . time() . '.pdf');
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            file_put_contents($tempPath, $pdfContent);
            
            // Configure Cloudinary
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key' => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => [
                    'secure' => true
                ]
            ]);
            
            // Upload to Cloudinary
            $uploadResult = $cloudinary->uploadApi()->upload(
                $tempPath,
                [
                    'resource_type' => 'raw',
                    'folder' => 'invoices',
                    'public_id' => 'invoice_' . $invoice->invoice_number . '_' . time(),
                    'format' => 'pdf',
                ]
            );
            
            // Delete temporary file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            return $uploadResult['secure_url'] ?? $uploadResult['url'] ?? null;
            
        } catch (\Exception $e) {
            \Log::error('PDF generation/upload error: ' . $e->getMessage());
            // Clean up temp file if it exists
            if (isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
            throw $e;
        }
    }

    /**
     * Generate QR code for PDF URL
     */
    public function generateQrCode(Request $request)
    {
        try {
            $url = $request->get('url');
            
            if (!$url) {
                return response()->json([
                    'success' => false,
                    'message' => 'URL шаардлагатай'
                ], 400);
            }
            
            // Use SVG format which doesn't require ImageMagick or GD
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(256)
                ->generate($url);
            
            // SVG can be embedded directly in HTML
            $svgData = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
            
            return response()->json([
                'success' => true,
                'qr_code' => $svgData
            ]);
            
        } catch (\Exception $e) {
            \Log::error('QR code generation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'QR код үүсгэхэд алдаа гарлаа: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proxy PDF from Cloudinary with proper headers
     * If PDF doesn't exist, generate it on-the-fly
     */
    public function viewPdf($uuid)
    {
        try {
            $invoice = Invoice::findByUuid($uuid);
            
            if (!$invoice) {
                abort(404, 'Нэхэмжлэл олдсонгүй');
            }
            
            $pdfContent = null;
            
            // Try to fetch from Cloudinary if pdf_url exists
            if ($invoice->pdf_url) {
                $pdfUrl = $invoice->pdf_url;
                
                // Fetch PDF from Cloudinary
                $ch = curl_init($pdfUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                
                $pdfContent = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);
                
                // If curl failed, log the error
                if ($curlError) {
                    \Log::warning('PDF curl error for invoice ' . $invoice->id . ' (UUID: ' . $uuid . '): ' . $curlError);
                }
                
                // If PDF fetch failed, try to generate on-the-fly
                if ($httpCode !== 200 || !$pdfContent || $curlError) {
                    \Log::info('PDF not available from Cloudinary for invoice ' . $invoice->id . ' (UUID: ' . $uuid . '), generating on-the-fly. HTTP Code: ' . $httpCode);
                    $pdfContent = null; // Will trigger on-the-fly generation
                }
            }
            
            // If PDF not available from Cloudinary, generate it on-the-fly
            if (!$pdfContent) {
                try {
                    // Ensure items are decoded
                    if (is_string($invoice->items)) {
                        $invoice->items = json_decode($invoice->items, true);
                    }
                    if (!is_array($invoice->items)) {
                        $invoice->items = [];
                    }
                    
                    // Ensure dates are Carbon instances
                    if ($invoice->invoice_date && !($invoice->invoice_date instanceof \Carbon\Carbon)) {
                        $invoice->invoice_date = \Carbon\Carbon::parse($invoice->invoice_date);
                    }
                    if ($invoice->due_date && !($invoice->due_date instanceof \Carbon\Carbon)) {
                        $invoice->due_date = \Carbon\Carbon::parse($invoice->due_date);
                    }
                    
                    // Generate PDF on-the-fly
                    $pdf = Pdf::loadView('admin.invoice.pdf', ['invoice' => $invoice]);
                    $pdfContent = $pdf->output();
                    
                    // Optionally try to upload to Cloudinary in the background
                    try {
                        $pdfUrl = $this->generateAndUploadPdf($invoice);
                        if ($pdfUrl) {
                            $invoice->update(['pdf_url' => $pdfUrl]);
                        }
                    } catch (\Exception $uploadError) {
                        // Don't fail if upload fails, just log it
                        \Log::warning('Failed to upload PDF to Cloudinary for invoice ' . $invoice->id . ' (UUID: ' . $uuid . '): ' . $uploadError->getMessage());
                    }
                } catch (\Exception $genError) {
                    \Log::error('PDF generation error for invoice ' . $invoice->id . ' (UUID: ' . $uuid . '): ' . $genError->getMessage());
                    \Log::error('Stack trace: ' . $genError->getTraceAsString());
                    abort(500, 'PDF файл үүсгэхэд алдаа гарлаа: ' . $genError->getMessage());
                }
            }
            
            if (!$pdfContent) {
                abort(500, 'PDF файл үүсгэх боломжгүй байна');
            }
            
            // Return PDF with proper headers
            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="invoice_' . $invoice->invoice_number . '.pdf"')
                ->header('Cache-Control', 'public, max-age=3600');
            
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            // Re-throw HTTP exceptions (abort)
            throw $e;
        } catch (\Exception $e) {
            \Log::error('PDF view error for invoice UUID ' . $uuid . ': ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'PDF файл харуулахад алдаа гарлаа: ' . $e->getMessage());
        }
    }

    /**
     * Delete an invoice
     */
    public function delete($id)
    {
        try {
            $invoice = Invoice::find($id);
            
            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }
            
            $invoice->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}