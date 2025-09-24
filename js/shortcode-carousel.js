jQuery(document).ready(function($){
    $('.dourousi-courses-carousel').slick({
        infinite: true,
        slidesToShow: 3, // Nombre de cartes affichées
        slidesToScroll: 1, // Nombre de cartes à défiler
        autoplay: true,
        autoplaySpeed: 3000,
        dots: true, // Affiche les points de navigation
        arrows: true, // Affiche les flèches de navigation
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    });
});