@if(isset($honor) && $honor)
<!-- Honor Modal -->
<div class="modal fade" id="honorModal" tabindex="-1" aria-labelledby="honorModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow honors-modal-content">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title honors-title" id="honorModalLabel">{{ $honor->title }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <div class="position-relative honor-image-container">
                    <img src="{{ getImage(getFilePath('honors') . '/' . $honor->image) }}" alt="{{ $honor->title }}" class="img-fluid w-100 honors-image">
                    <div class="honor-overlay"></div>
                    <div class="honors-badge">
                        <i class="las la-award"></i>
                    </div>
                </div>
                
                @if($honor->description)
                <div class="honor-description p-4">
                    <p class="honors-text mb-0">{{ $honor->description }}</p>
                </div>
                @endif

                <div class="honors-confetti" id="confetti-canvas"></div>
            </div>
            <div class="modal-footer border-0 justify-content-center p-4">
                <button type="button" class="btn btn-primary px-5 py-3 fw-bold honors-button" data-bs-dismiss="modal">
                    <i class="las la-check-circle me-2"></i> Tiếp tục
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .honors-modal-content {
        overflow: hidden;
        border-radius: 16px;
        transform: scale(0.9);
        opacity: 0;
        transition: all 0.3s ease-in-out;
    }
    
    .modal.show .honors-modal-content {
        transform: scale(1);
        opacity: 1;
    }
    
    .honors-title {
        font-weight: 700;
        opacity: 0;
        transform: translateY(-20px);
        animation: fadeInDown 0.6s ease forwards 0.3s;
    }
    
    .honor-image-container {
        position: relative;
        max-height: 70vh;
        overflow: hidden;
        border-bottom: 4px solid rgba(0,0,0,0.05);
    }
    
    .honors-image {
        transform: scale(1.05);
        transition: transform 10s ease;
    }
    
    .modal.show .honors-image {
        transform: scale(1);
    }
    
    .honor-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0) 60%, rgba(0,0,0,0.2) 100%);
        pointer-events: none;
    }
    
    .honors-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #FFD700, #FFA500);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        opacity: 0;
        transform: translateY(-20px) rotate(45deg);
        animation: badgeIn 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards 0.5s;
    }
    
    .honors-badge i {
        font-size: 32px;
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .honor-description {
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .honors-text {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease forwards 0.6s;
        font-size: 16px;
        line-height: 1.6;
    }
    
    .honors-button {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        transition: all 0.3s ease;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease forwards 0.8s;
    }
    
    .honors-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    }
    
    .honors-button:active {
        transform: translateY(0);
    }

    .honors-confetti {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 10;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes badgeIn {
        0% {
            opacity: 0;
            transform: translateY(-20px) rotate(45deg);
        }
        70% {
            opacity: 1;
            transform: translateY(5px) rotate(-5deg);
        }
        85% {
            transform: translateY(-2px) rotate(5deg);
        }
        100% {
            opacity: 1;
            transform: translateY(0) rotate(0);
        }
    }
    
    @media (max-width: 767px) {
        .honor-image-container {
            max-height: 50vh;
        }
        
        .honors-badge {
            width: 50px;
            height: 50px;
        }
        
        .honors-badge i {
            font-size: 26px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    // Show the modal when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        var honorModal = new bootstrap.Modal(document.getElementById('honorModal'));
        honorModal.show();
        
        // Launch confetti when modal is shown
        honorModal._element.addEventListener('shown.bs.modal', function() {
            launchConfetti();
        });
        
        // Function to launch confetti
        function launchConfetti() {
            const canvas = document.getElementById('confetti-canvas');
            
            // First burst
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#FFD700', '#FFA500', '#007bff', '#28a745', '#dc3545']
            });
            
            // Second burst after a delay
            setTimeout(() => {
                confetti({
                    particleCount: 50,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0, y: 0.6 },
                    colors: ['#FFD700', '#FFA500', '#007bff']
                });
                
                confetti({
                    particleCount: 50,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1, y: 0.6 },
                    colors: ['#FFD700', '#FFA500', '#007bff']
                });
            }, 500);
        }
    });
</script>
@endif 