<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smooth Sliding Coverflow</title>
    <link rel="stylesheet" href="carousel.css">
</head>
<body>
    <div class="coverflow-container">
        <div class="coverflow-slide"></div>
        <div class="coverflow-slide"></div>
        <div class="coverflow-slide"></div>
        <div class="coverflow-slide"></div>
       
    </div>
    <script>
        let currentIndex = 2;
        const slides = document.querySelectorAll('.coverflow-slide');

        function updateSlides() {
            slides.forEach((slide, index) => {
                slide.classList.remove('active', 'left', 'right');
                if (index === currentIndex) {
                    slide.classList.add('active');
                } else if (index === (currentIndex - 1 + slides.length) % slides.length) {
                    slide.classList.add('left');
                } else if (index === (currentIndex + 1) % slides.length) {
                    slide.classList.add('right');
                }
            });
        }

        function autoSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            updateSlides();
        }

        setInterval(autoSlide, 3000);
        updateSlides();
    </script>
</body>
</html>
