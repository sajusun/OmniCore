<div>
    <div class="mb-3">
        <label for="{{ $name }}" class="form-label fw-medium">{{ $label }}:</label>

        {{-- FILE INPUT --}}
        <input
            type="file"
            @if(isset($multiple) && $multiple) multiple @endif
            class="form-control @error($name) is-invalid @enderror"
            name="{{ $name }}@if(isset($multiple) && $multiple)[]@endif"
            id="{{ $name }}"
        />

        {{ $slot }}

        @if(isset($file) && is_array($file) && count($file) > 0)
            <div class="row mt-3 old-images-wrapper">

                @foreach($file as $key => $img)
                    <div class="col-md-3 col-6 mb-3 position-relative old-image-box">

                        <img src="{{ asset($img) }}"
                             class="img-thumbnail shadow-sm old-image"
                             style="height: 150px; width:100%; object-fit:cover;">

                        {{-- Delete Button --}}
                        <button type="button"
                                class="btn btn-danger btn-sm position-absolute delete-old-image"
                                style="top: 5px; right: 10px;"
                                data-image="{{ $img }}">
                            <i class="bi bi-trash"></i>
                        </button>

                    </div>
                @endforeach

            </div>
        @endif

        {{-- PREVIEW FOR NEWLY SELECTED IMAGES --}}
        <div class="row mt-3 new-images-preview" style="display:none;"></div>

        @error($name)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- JAVASCRIPT (Preview + Delete Logic) --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    let input = document.getElementById("{{ $name }}");

    input.addEventListener("change", function (event) {
        let previewWrapper = document.querySelector(".new-images-preview");
        previewWrapper.innerHTML = ""; // clear old previews

        if (event.target.files.length > 0) {
            previewWrapper.style.display = "flex";
        }

        [...event.target.files].forEach(file => {
            let reader = new FileReader();
            reader.onload = e => {
                let col = document.createElement("div");
                col.classList.add("col-md-3", "col-6", "mb-3");

                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}"
                             class="img-thumbnail shadow-sm"
                             style="height: 150px; width:100%; object-fit:cover;">
                    </div>
                `;

                previewWrapper.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    });
});

</script>
