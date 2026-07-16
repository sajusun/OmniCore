@extends('backend.app')

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">Product Details</h1>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th>UPC</th><td>{{ $product->upc }}</td></tr>
                        <tr><th>Name</th><td>{{ $product->name }}</td></tr>
                        <tr><th>Brand</th><td>{{ $product->brand }}</td></tr>
                        <tr><th>Serving Size</th><td>{{ $product->serving_size_text }}</td></tr>
                        <tr><th>Score</th><td>{{ $product->score }}</td></tr>
                        <tr><th>Verdict</th><td>{{ $product->verdict }}</td></tr>
                        <tr><th>Image</th><td>
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="Product Image" style="max-height:150px;">
                            @else
                                N/A
                            @endif
                        </td></tr>
                        <tr><th>Calories</th><td>{{ $product->calories }}</td></tr>
                        <tr><th>Protein (g)</th><td>{{ $product->protein_g }}</td></tr>
                        <tr><th>Carbs (g)</th><td>{{ $product->carbs_g }}</td></tr>
                        <tr><th>Fiber (g)</th><td>{{ $product->fiber_g }}</td></tr>
                        <tr><th>Sugar (g)</th><td>{{ $product->sugar_g }}</td></tr>
                        <tr><th>Sugar Alcohol (g)</th><td>{{ $product->sugar_alcohol_g }}</td></tr>
                        <tr><th>Fat (g)</th><td>{{ $product->fat_g }}</td></tr>
                        <tr><th>Saturated Fat (g)</th><td>{{ $product->sat_fat_g }}</td></tr>
                        <tr><th>Seed Oil Score</th><td>{{ $product->seed_oil_score }}</td></tr>
                        <tr><th>Ingredients</th><td><pre class="mb-0">{{ $product->ingredients_text }}</pre></td></tr>
                        <tr><th>Source</th><td>{{ $product->source }}</td></tr>
                        <tr><th>Last Seen At</th><td>{{ $product->last_seen_at }}</td></tr>
                        <tr><th>Trigger Flags</th><td><pre class="mb-0">{{ json_encode($product->trigger_flags, JSON_PRETTY_PRINT) }}</pre></td></tr>
                        <tr><th>Explanations</th><td><pre class="mb-0">{{ json_encode($product->explanations, JSON_PRETTY_PRINT) }}</pre></td></tr>
                    </table>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mt-3">Back to List</a>
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary mt-3">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
