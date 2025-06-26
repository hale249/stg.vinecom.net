@if(isset($honor) && $honor)
<!-- Honor Modal -->
<div class="modal fade" id="honorModal" tabindex="-1" aria-labelledby="honorModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="honorModalLabel">{{ $honor->title }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <div class="position-relative honor-image-container">
                    <img src="{{ getImage(getFilePath('honors') . '/' . $honor->image) }}" alt="{{ $honor->title }}" class="img-fluid w-100">
                    <div class="honor-overlay"></div>
                </div>
                
                @if($honor->description)
                <div class="honor-description p-4 bg-light">
                    <p>{{ $honor->description }}</p>
                </div>
                @endif
            </div>
            <div class="modal-footer border-0 justify-content-center p-4">
                <button type="button" class="btn btn-primary px-5 py-3 fw-bold" data-bs-dismiss="modal">
                    <i class="las la-check-circle me-2"></i> Tiếp tục
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .honor-image-container {
        position: relative;
        max-height: 70vh;
        overflow: hidden;
    }
    
    .honor-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0) 70%, rgba(0,0,0,0.1) 100%);
        pointer-events: none;
    }
    
    @media (max-width: 767px) {
        .honor-image-container {
            max-height: 50vh;
        }
    }
</style>

<script>
    // Show the modal when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        var honorModal = new bootstrap.Modal(document.getElementById('honorModal'));
        honorModal.show();
    });
</script>
@endif 