<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Bug;
use App\Models\BugAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BugController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = Bug::query();

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('reported_by', 'like', "%{$search}%")
                      ->orWhere('assigned_to', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $bugs = $query->paginate(15);

            // Get statistics
            $stats = [
                'total' => Bug::count(),
                'open' => Bug::open()->count(),
                'in_progress' => Bug::inProgress()->count(),
                'resolved' => Bug::resolved()->count(),
                'closed' => Bug::closed()->count(),
                'critical' => Bug::byPriority('critical')->count(),
                'high' => Bug::byPriority('high')->count(),
                'medium' => Bug::byPriority('medium')->count(),
                'low' => Bug::byPriority('low')->count(),
            ];

            return view('super-admin.bugs.index', compact('bugs', 'stats'));
        } catch (\Exception $e) {
            Log::error('Bug index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading bugs.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('super-admin.bugs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validationRules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:open,in_progress,testing,resolved,closed',
                'priority' => 'required|in:low,medium,high,critical',
                'type' => 'required|in:bug,feature_request,improvement,task',
                'reported_by' => 'nullable|string|max:255',
                'assigned_to' => 'nullable|string|max:255',
                'steps_to_reproduce' => 'nullable|string',
                'expected_behavior' => 'nullable|string',
                'actual_behavior' => 'nullable|string',
            ];

            // Only add file validation if files are present
            if ($request->hasFile('attachments')) {
                $validationRules['attachments.*'] = 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,avi,mov,wmv,webm';
            }

            $request->validate($validationRules);

            $bug = Bug::create($request->except('attachments'));

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                $this->handleFileUploads($bug, $request->file('attachments'));
            }

            Log::info('Bug created successfully', ['bug_id' => $bug->id]);

            return redirect()->route('super-admin.bugs.index')
                ->with('success', 'Bug created successfully!');
        } catch (\Exception $e) {
            Log::error('Bug creation error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the bug.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Http\Response
     */
    public function show(Bug $bug)
    {
        return view('super-admin.bugs.show', compact('bug'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Http\Response
     */
    public function edit(Bug $bug)
    {
        return view('super-admin.bugs.edit', compact('bug'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bug $bug)
    {
        try {
            $validationRules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:open,in_progress,testing,resolved,closed',
                'priority' => 'required|in:low,medium,high,critical',
                'type' => 'required|in:bug,feature_request,improvement,task',
                'reported_by' => 'nullable|string|max:255',
                'assigned_to' => 'nullable|string|max:255',
                'steps_to_reproduce' => 'nullable|string',
                'expected_behavior' => 'nullable|string',
                'actual_behavior' => 'nullable|string',
                'resolution_notes' => 'nullable|string',
            ];

            // Only add file validation if files are present
            if ($request->hasFile('attachments')) {
                $validationRules['attachments.*'] = 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,avi,mov,wmv,webm';
            }

            $request->validate($validationRules);

            $data = $request->except('attachments');

            // Set resolved_at timestamp if status is resolved or closed
            if (in_array($data['status'], ['resolved', 'closed']) && !$bug->resolved_at) {
                $data['resolved_at'] = now();
            } elseif (!in_array($data['status'], ['resolved', 'closed'])) {
                $data['resolved_at'] = null;
            }

            $bug->update($data);

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                $this->handleFileUploads($bug, $request->file('attachments'));
            }

            Log::info('Bug updated successfully', ['bug_id' => $bug->id]);

            return redirect()->route('super-admin.bugs.index')
                ->with('success', 'Bug updated successfully!');
        } catch (\Exception $e) {
            Log::error('Bug update error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the bug.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bug $bug)
    {
        try {
            $bug->delete();

            Log::info('Bug deleted successfully', ['bug_id' => $bug->id]);

            return redirect()->route('super-admin.bugs.index')
                ->with('success', 'Bug deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Bug deletion error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the bug.');
        }
    }

    /**
     * Update bug status via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bug  $bug
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Bug $bug)
    {
        try {
            $request->validate([
                'status' => 'required|in:open,in_progress,testing,resolved,closed'
            ]);

            $data = ['status' => $request->status];

            // Set resolved_at timestamp if status is resolved or closed
            if (in_array($request->status, ['resolved', 'closed']) && !$bug->resolved_at) {
                $data['resolved_at'] = now();
            } elseif (!in_array($request->status, ['resolved', 'closed'])) {
                $data['resolved_at'] = null;
            }

            $bug->update($data);

            Log::info('Bug status updated successfully', [
                'bug_id' => $bug->id,
                'new_status' => $bug->status
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bug status updated successfully!',
                    'status' => $bug->status,
                    'status_badge' => $bug->status_badge
                ]);
            }

            return redirect()->route('super-admin.bugs.index')
                ->with('success', 'Bug status updated successfully!');
        } catch (\Exception $e) {
            Log::error('Bug status update error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('super-admin.bugs.index')
                ->with('error', 'An error occurred while updating status.');
        }
    }

    /**
     * Handle file uploads for bug attachments
     *
     * @param Bug $bug
     * @param array $files
     * @return void
     */
    private function handleFileUploads(Bug $bug, array $files)
    {
        foreach ($files as $file) {
            if ($file->isValid()) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $filePath = 'bug-attachments/' . $bug->id . '/' . $fileName;
                
                // Store the file
                $storedPath = $file->storeAs('bug-attachments/' . $bug->id, $fileName, 'public');
                
                // Generate thumbnail for images
                $thumbnailPath = null;
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    try {
                        $thumbnailPath = $this->generateThumbnail($file, $bug->id, $fileName);
                    } catch (\Exception $e) {
                        Log::error('Thumbnail generation failed for file: ' . $fileName . ' - ' . $e->getMessage());
                        // Continue without thumbnail
                    }
                }
                
                // Create attachment record
                BugAttachment::create([
                    'bug_id' => $bug->id,
                    'original_name' => $originalName,
                    'file_name' => $fileName,
                    'file_path' => $storedPath,
                    'file_type' => $file->getClientOriginalExtension(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'thumbnail_path' => $thumbnailPath,
                ]);
            }
        }
    }

    /**
     * Generate thumbnail for image files
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $bugId
     * @param string $fileName
     * @return string|null
     */
    private function generateThumbnail($file, $bugId, $fileName)
    {
        try {
            $thumbnailName = 'thumb_' . $fileName;
            $thumbnailPath = 'bug-attachments/' . $bugId . '/thumbnails/' . $thumbnailName;
            
            // Create thumbnail directory if it doesn't exist
            Storage::disk('public')->makeDirectory('bug-attachments/' . $bugId . '/thumbnails');
            
            // Generate thumbnail using Intervention Image v2.7.2
            $image = Image::make($file->getRealPath());
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            $image->save(storage_path('app/public/' . $thumbnailPath));
            
            return $thumbnailPath;
        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed: ' . $e->getMessage());
            // Return null if thumbnail generation fails, but don't break the upload
            return null;
        }
    }

    /**
     * Delete an attachment
     *
     * @param BugAttachment $attachment
     * @return \Illuminate\Http\Response
     */
    public function deleteAttachment(BugAttachment $attachment)
    {
        try {
            // Try to delete the files first
            $fileDeleted = $attachment->deleteFile();
            
            // Delete the database record regardless of file deletion success
            $attachment->delete();
            
            if ($fileDeleted) {
                Log::info('Attachment deleted successfully', ['attachment_id' => $attachment->id]);
                return response()->json([
                    'success' => true,
                    'message' => 'Attachment deleted successfully!'
                ]);
            } else {
                Log::warning('Attachment record deleted but files may not have been removed', ['attachment_id' => $attachment->id]);
                return response()->json([
                    'success' => true,
                    'message' => 'Attachment deleted successfully! (Note: Some files may not have been removed from storage)'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Attachment deletion error: ' . $e->getMessage(), [
                'attachment_id' => $attachment->id ?? 'unknown',
                'exception' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the attachment: ' . $e->getMessage()
            ], 500);
        }
    }
}
