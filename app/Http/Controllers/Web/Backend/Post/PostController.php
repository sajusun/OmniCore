<?php

namespace App\Http\Controllers\Web\Backend\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\PostService;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    public function index(Request $request)
    {
        $userId = $request->query('user') ?? $request->query('user_id');
        $selectedUser = $userId ? User::find($userId) : null;

        if ($request->ajax()) {
            $query = $this->postService->getPostsForAdmin([
                'user'    => $userId,
                'user_id' => $userId,
                'status'  => $request->query('status'),
            ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('media', function ($row) {
                    if (!empty($row->thumbnail)) {
                        $url = asset($row->thumbnail);
                        return '<img src="' . $url . '" class="rounded" style="width: 42px; height: 42px; object-fit: cover; border: 1px solid #e2e8f0;">';
                    }
                    $firstMedia = $row->media->first();
                    if ($firstMedia) {
                        $url = !empty($firstMedia->full_url) ? $firstMedia->full_url : (!empty($firstMedia->url) ? $firstMedia->url : asset($firstMedia->path));
                        $extraCount = $row->media->count() - 1;
                        $extraBadge = $extraCount > 0 ? '<span class="badge bg-dark ms-1">+' . $extraCount . '</span>' : '';
                        return '<div class="d-flex align-items-center">
                                    <img src="' . $url . '" class="rounded" style="width: 42px; height: 42px; object-fit: cover; border: 1px solid #e2e8f0;" onError="this.parentElement.innerHTML=\'<span class=\\\''.'text-muted small\\\'>Media</span>\';">' . $extraBadge . '
                                </div>';
                    }
                    return '<span class="text-muted small">No Media</span>';
                })
                ->addColumn('user', function ($row) {
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->addColumn('title', function ($row) {
                    if (!empty($row->title)) {
                        return Str::limit($row->title, 40);
                    }
                    $text = strip_tags($row->content ?? '');
                    return !empty($text) ? Str::limit($text, 50) : 'Post #' . $row->id;
                })
                ->addColumn('type', function ($row) {
                    $typeVal = is_object($row->type) ? ($row->type->value ?? $row->type->name ?? 'post') : ($row->type ?? 'post');
                    return '<span class="badge bg-info">' . ucfirst((string) $typeVal) . '</span>';
                })
                ->addColumn('visibility', function ($row) {
                    return '<span class="badge bg-secondary">' . ucfirst((string) ($row->visibility ?? 'public')) . '</span>';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M, Y H:i') : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.posts.show', $row->id);
                    return '<div class="d-flex align-items-center gap-2">
                                <a href="' . $showUrl . '" class="btn btn-sm btn-info" title="View Details"><i class="fa fa-eye"></i></a>
                                <button type="button" onclick="confirmDeletePost(' . $row->id . ')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['media', 'type', 'visibility', 'action'])
                ->make(true);
        }

        return view('backend.posts.index', compact('selectedUser', 'userId'));
    }

    public function show(Post $post)
    {
        $post = $this->postService->show($post);
        return view('backend.posts.show', compact('post'));
    }

    public function destroy(int $id)
    {
        $post = Post::findOrFail($id);
        $result = $this->postService->destroy($post);
        if (!$result) {
            return response()->json(['status' => false, 'message' => 'Operation Failed!']);
        }

        return response()->json(['status' => true, 'message' => 'Post Deleted Successfully!']);
    }
}
