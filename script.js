
document.addEventListener('DOMContentLoaded', function () {
    // Use GSAP to animate the registration card
    gsap.from("#xCard", {
        duration: 1, // Animation duration in seconds
        opacity: 0, // Starting opacity
        y: 20, // Starting position (20 pixels down)
        ease: "power2.out" // Easing function
    });

    gsap.from("#yCard", {
        duration: 1, // Animation duration in seconds
        opacity: 0, // Starting opacity
        y: -20, // Starting position (20 pixels up)
        ease: "power2.out" // Easing function
    });
    gsap.to("#yCard", {
        duration: 1, // Animation duration in seconds
        x: -10, // Move to the right by 10% of the card's width
        ease: "power2.out", // Easing function
        delay: 1 // Delay the start of this animation by 3 seconds
    });
    if(user===""){    
        }
    else{
        gsap.from("#qCard", {
            duration: 1, // Animation duration in seconds
            opacity: 0, // Starting opacity
            y:-20, // Starting position (20 pixels down)
            ease: "power2.out" // Easing function
        });
      
        gsap.from("#wCard", {
            duration: 1, // Animation duration in seconds
            opacity: 0, // Starting opacity
            y: 20, // Starting position (20 pixels up)
            ease: "power2.out" // Easing function
        }); 
        
        gsap.to("#qCard", {
            duration: 1,
            text: userID,
            ease: "none",
            delay: 2,
          });
          gsap.to("#wCard", {
            duration: 1,
            text: userID,
            ease: "none",
            delay: 2,
          })
    }
});