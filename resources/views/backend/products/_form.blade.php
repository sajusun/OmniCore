<div class="row mb-3">
    <div class="col-md-6">
        <label for="upc" class="form-label">UPC</label>
        <input type="text" name="upc" id="upc" class="form-control" value="{{ old('upc', $product->upc ?? '') }}"
            required>
    </div>
    <div class="col-md-6">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name ?? '') }}"
            required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="brand" class="form-label">Brand</label>
        <input type="text" name="brand" id="brand" class="form-control"
            value="{{ old('brand', $product->brand ?? '') }}">
    </div>
    {{-- <div class="col-md-6">
        <label for="image" class="form-label">Image</label>
        <input type="file" name="image" id="image" class="form-control">
        @if(isset($product) && $product->image_url)
        <div class="mt-2">
            <img src="{{ $product->image_url }}" alt="Current Image" style="height:80px;">
        </div>
        @endif
    </div> --}}
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="serving_size_text" class="form-label">Serving Size</label>
        <input type="text" name="serving_size_text" id="serving_size_text" class="form-control"
            value="{{ old('serving_size_text', $product->serving_size_text ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="verdict" class="form-label">Verdict</label>
        <select name="verdict" id="verdict" class="form-select">
            @foreach($verdicts as $v)
            <option value="{{ $v->value }}" {{ (old('verdict', $product->verdict ?? '') == $v->value) ? 'selected' : ''
                }}>{{ ucfirst($v->value) }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="calories" class="form-label">Calories</label>
        <input type="number" step="0.01" name="calories" id="calories" class="form-control"
            value="{{ old('calories', $product->calories ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="protein_g" class="form-label">Protein (g)</label>
        <input type="number" step="0.01" name="protein_g" id="protein_g" class="form-control"
            value="{{ old('protein_g', $product->protein_g ?? '') }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="fiber_g" class="form-label">Fiber (g)</label>
        <input type="number" step="0.01" name="fiber_g" id="fiber_g" class="form-control"
            value="{{ old('fiber_g', $product->fiber_g ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="sugar_g" class="form-label">Sugar (g)</label>
        <input type="number" step="0.01" name="sugar_g" id="sugar_g" class="form-control"
            value="{{ old('sugar_g', $product->sugar_g ?? '') }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="carbs_g" class="form-label">Carbs (g)</label>
        <input type="number" step="0.01" name="carbs_g" id="carbs_g" class="form-control"
            value="{{ old('carbs_g', $product->carbs_g ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="fat_g" class="form-label">Fat (g)</label>
        <input type="number" step="0.01" name="fat_g" id="fat_g" class="form-control"
            value="{{ old('fat_g', $product->fat_g ?? '') }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="ingredients_text" class="form-label">Ingredients</label>
        <textarea name="ingredients_text" id="ingredients_text" class="form-control"
            rows="3">{{ old('ingredients_text', $product->ingredients_text ?? '') }}</textarea>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="source" class="form-label">Source</label>
        <input type="text" name="source" id="source" class="form-control"
            value="{{ old('source', $product->source ?? '') }}">
    </div>
    <div class="col-md-6">
        <label for="score" class="form-label">Score</label>
        <input type="number" step="0.01" name="score" id="score" class="form-control"
            value="{{ old('score', $product->score ?? '') }}">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label class="form-label">Explanations (Key - Value)</label>
        <div id="explanations-container">
            @php
                $explanations = old('explanations', isset($product) ? $product->explanations : []);
                if (is_string($explanations)) $explanations = json_decode($explanations, true);
                if (!is_array($explanations)) $explanations = [];
            @endphp
            @foreach($explanations as $key => $val)
                <div class="input-group mb-2 kv-row">
                    <input type="text" name="explanations_keys[]" class="form-control" value="{{ $key }}" placeholder="Key">
                    <input type="text" name="explanations_values[]" class="form-control" value="{{ is_array($val) ? json_encode($val) : $val }}" placeholder="Value">
                    <button type="button" class="btn btn-danger remove-kv">X</button>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-secondary mt-1" id="add-explanation">Add Explanation</button>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <label class="form-label">Trigger Flags (Key - Value)</label>
        <div id="trigger-flags-container">
            @php
                $flags = old('trigger_flags', isset($product) ? $product->trigger_flags : []);
                if (is_string($flags)) $flags = json_decode($flags, true);
                if (!is_array($flags)) $flags = [];
            @endphp
            @foreach($flags as $key => $val)
                <div class="input-group mb-2 kv-row">
                    <input type="text" name="trigger_flags_keys[]" class="form-control" value="{{ $key }}" placeholder="Key">
                    <input type="text" name="trigger_flags_values[]" class="form-control" value="{{ is_array($val) ? json_encode($val) : $val }}" placeholder="Value">
                    <button type="button" class="btn btn-danger remove-kv">X</button>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-secondary mt-1" id="add-trigger-flag">Add Trigger Flag</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function addRow(containerId, keyName, valueName) {
        const container = document.getElementById(containerId);
        const row = document.createElement('div');
        row.className = 'input-group mb-2 kv-row';
        row.innerHTML = `
            <input type="text" name="${keyName}[]" class="form-control" placeholder="Key">
            <input type="text" name="${valueName}[]" class="form-control" placeholder="Value">
            <button type="button" class="btn btn-danger remove-kv">X</button>
        `;
        container.appendChild(row);
    }

    document.getElementById('add-explanation').addEventListener('click', function() {
        addRow('explanations-container', 'explanations_keys', 'explanations_values');
    });

    document.getElementById('add-trigger-flag').addEventListener('click', function() {
        addRow('trigger-flags-container', 'trigger_flags_keys', 'trigger_flags_values');
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-kv')) {
            e.target.closest('.kv-row').remove();
        }
    });
});
</script>

<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="image" class="form-label">Image:</label>
            <input type="file" class="dropify form-control @error('image_url') is-invalid @enderror"
                data-default-file="{{ !empty($product->image_url) && file_exists(public_path($product->image_url)) ? asset($product->image_url) : asset('default/logo.png') }}"
                name="image" id="image">
            <p class="textTransform">Image Size Less than 5MB and Image Type must be jpeg,jpg,png.</p>
            @error('favicon')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>