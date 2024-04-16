import './bootstrap';
import jQuery from 'jquery';
window.$ = jQuery;


$(document).ready(function(){
    $(document).on('click', '#close-flash-message', function(){
        $('#flash-message').hide();
    });
});

document.addEventListener('click', function(event) {
    var myDiv = document.getElementById('myDiv');
    if (!myDiv) return; // Check if myDiv exists

    var isClickInside = myDiv.contains(event.target);

    if (!isClickInside) {
        // The click was outside the div, handle it
        myDiv.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    let myDiv = document.getElementById("svg");
    const container = document.querySelector('.navwrap ');
    const buttons = document.querySelectorAll('.menu-toggle');

    if (!myDiv || !container) return; // Check if elements exist

    // Set the transform origin to the top of the SVG
    myDiv.style.transformOrigin = "top";

    buttons.forEach(button => {
        button.addEventListener('mouseover', (e) => {
            // Get the button's position
            const buttonRect = button.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();

            // Set the SVG's position to match the button's position
            myDiv.style.left = (buttonRect.left - containerRect.left) + "px";

            // Restore the original scale of the SVG
            myDiv.style.transform = "scaleY(1)";
        });

        button.addEventListener('mouseout', (e) => {
            // Scale the SVG to a height of 0
            myDiv.style.transform = "scaleY(0)";
        });
    });

    container.addEventListener("mouseout", (e) => {
        // Scale the SVG to a height of 0
        myDiv.style.transform = "scaleY(0)";
    });

    container.addEventListener("mouseover", (e) => {
        // Restore the original scale of the SVG
        myDiv.style.transform = "scaleY(1)";
    });
});

// function initializeSlickSlider() {
//     $('.slick-slider').on('init', function() {
//         const cards = Array.from(document.getElementsByClassName('product-card'));
//         const maxHeight = Math.max(...cards.map(card => card.offsetHeight));
//         cards.forEach(card => card.style.height = `${maxHeight}px`);
//     }).slick({
//         infinite: false,
//         slidesToShow: 5,
//         slidesToScroll: 5,
//         dots: false,
//         prevArrow: false,
//         nextArrow: false,
    
//         responsive: [
//             {
//                 breakpoint: 1300,
//                 settings: {
//                     slidesToShow: 4,
//                     slidesToScroll: 4
//                 }
//             },
//             {
//                 breakpoint: 1100,
//                 settings: {
//                     slidesToShow: 3,
//                     slidesToScroll: 3
//                 }
//             },
//             {
//                 breakpoint: 768,
//                 settings: {
//                     slidesToShow: 2,
//                     slidesToScroll: 2
//                 }
//             },
//             {
//                 breakpoint: 576,
//                 settings: {
//                     slidesToShow: 1,
//                     slidesToScroll: 1
//                 }
//             }
//         ]
//     });
// }

