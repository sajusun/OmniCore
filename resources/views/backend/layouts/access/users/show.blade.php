@extends('backend.app', ['title' => 'Show User'])

@section('content')

<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">

        <!-- CONTAINER -->
        <div class="main-container container-fluid">

            <div class="page-header">
                <div>
                    <h1 class="page-title">User</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">User</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Show</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <img src="{{ asset($user->avatar ?? 'default/profile.jpg') }}" class="img-fluid" alt="{{ $user->name?? 'N/A' }}">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card product-sales-main">
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Roles</th>
                                    <td>
                                        @forelse ($user->roles as $role)
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                        @empty
                                        <span class="badge bg-primary">N/A</span>
                                        @endforelse
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <a href="{{ route('admin.users.status', $user->id) }}" class="btn btn-warning">
                                            @if ($user->status == 'active')
                                            <i class="fa-solid fa-lock-open"></i>
                                            @else
                                            <i class="fa-solid fa-lock"></i>
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Affiliate</th>
                                    <td><button data-clipboard-text="{{ $user->slug ? url('affiliate/' . $user->slug) : 'N/A' }}" class="btn btn-success copy-btn" {{ $user->slug ? '' : 'disabled' }}><i class="fa-regular fa-copy"></i></button></td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $user->created_at ? $user->created_at : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $user->updated_at ? $user->updated_at : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div><!-- COL END -->
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card product-sales-main">
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>{{ $user->profile->dob ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>{{ $user->profile->gender ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div><!-- COL END -->
            </div>

        </div>
    </div>
</div>
<!-- CONTAINER CLOSED -->
@endsection
@push('scripts')
<script type="text/javascript">
    const copyBtns = document.querySelectorAll(".copy-btn");

    if (copyBtns.length > 0) {
        copyBtns.forEach(copyBtn => {
            copyBtn.addEventListener("click", async function() {
                try {
                    const copyText = this.dataset.clipboardText;

                    // Await the writeText call directly instead of using .then
                    await navigator.clipboard.writeText(copyText);

                    alert("Copied to clipboard!");
                } catch (error) {
                    // Handling errors properly
                    console.error("Error copying text: ", error);
                }
            });
        });
    }
</script>
@endpush
