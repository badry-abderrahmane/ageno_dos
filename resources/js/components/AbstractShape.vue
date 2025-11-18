<script setup lang="ts">
// Assuming you have defineProps and other script setup from your previous components
// No specific script logic is needed for this static hero section, just styling.
</script>

<template>
  <div class="all">
    <div class="diamond">
      <svg height="100%" width="100%" class="uno">
        <polygon points="50,200 200,0 130,250" />
      </svg>
      <svg height="100%" width="100%" class="dos">
        <polygon points="30,250 100,0 180,250" />
      </svg>
      <svg height="100%" width="100%" class="tres">
        <polygon points="50,200 200,0 130,250" />
      </svg>
      <svg height="100%" width="100%" class="cuatro">
        <polygon points="50,200 200,500 130,250" />
      </svg>
      <svg height="100%" width="100%" class="cinco">
        <polygon points="30,250 100,0 180,250" />
      </svg>
      <svg height="100%" width="100%" class="seis">
        <polygon points="50,200 200,500 130,250" />
      </svg>

      <div class="aurora"></div>
      <div class="aurora2"></div>
      <div class="aurora3"></div>

    </div>
    <div class="sombra"></div>
  </div>
</template>

<style scoped>
/* Scoped styles for the geometric shapes */

/* Bounce Animation for the Diamond */
@keyframes movimiento {
  0% {
    top: 0
  }

  50% {
    top: 30px
  }

  100% {
    top: 0;
  }
}

/* Shadow Pulse Animation */
@keyframes sombra {
  0% {
    transform: scale(.7);
  }

  50% {
    transform: scale(.9);
  }

  100% {
    transform: scale(.7);
  }
}

.all {
  display: flex;
  position: relative;
  width: 100%;
  height: 100vh;
  align-items: center;
  justify-content: center;
  background-color: #171717;
  /* Dark background */

  .diamond {
    width: 400px;
    height: 500px;
    margin-top: -50px;
    background-color: transparent;
    position: relative;
    display: inherit;
    animation: movimiento 2s ease infinite;

    polygon {
      /* Adjusted stroke to a bright, transparent accent green */
      stroke: rgba(52, 211, 153, 0.2);
      stroke-width: 3px;
      stroke-dasharray: 0;
      stroke-linejoin: round;
    }

    svg {
      /* Increased brightness and contrast for a sharper look */
      filter: brightness(2.5) hue-rotate(-30deg) contrast(1.6);
      z-index: 1;
    }

    /* Darkest facets for deep shadows (Deep Emerald) */
    .uno {
      fill: #064E3B;
      /* Deep Shadow 1 */
      left: 0px;
      position: relative;
    }

    /* Medium-Dark facets */
    .dos {
      position: absolute;
      top: 0;
      left: 100px;
      fill: #10B981;
      /* Medium Shadow */
    }

    /* Very Dark/Nearly Black for extreme corners */
    .tres {
      position: absolute;
      left: 0px;
      top: 0;
      transform: scaleX(-1);
      fill: #047857;
      /* Very Dark Shadow */
    }

    /* Medium-Light facets */
    .cuatro {
      position: absolute;
      top: 0;
      left: 0;
      fill: #34D399;
      /* Medium Light */
    }

    /* Dark corner facets */
    .cinco {
      position: absolute;
      top: 0;
      left: 100px;
      transform: scaleY(-1);
      fill: #065F46;
      /* Dark Corner */
    }

    /* Brightest accent facet (Lime/Light Emerald) */
    .seis {
      position: absolute;
      top: 0;
      left: 0px;
      transform: scaleX(-1);
      fill: #0dc21c;
      /* Accent Highlight */
    }

    /* Main Glow: Intense Accent Green */
    .aurora {
      width: 190px;
      height: 400px;
      /* Box shadow changed to a vibrant green */
      box-shadow: 0px 0px 100px 10px #10B981, #10B981 0px 0px 100px inset;
      position: absolute;
      top: 30px;
      margin: auto;
      left: 0;
      right: 0;
      border-radius: 100%;
      display: initial;
      /* Gradient shifted to bright lime and emerald */
      background: linear-gradient(to bottom, #99dd12, #34D399);
      z-index: 0;
      filter: blur(40px);
      opacity: .6;
    }

    /* Secondary Stripe 1: Bright Lime */
    .aurora2 {
      z-index: 2;
      width: 30px;
      height: 230px;
      margin: auto;
      left: -70px;
      right: 0;
      top: 5%;
      /* Gradient shifted to bright lime */
      background: linear-gradient(to right, #BEF264, #478915);
      display: block;
      border-radius: 30px;
      opacity: .45;
      position: absolute;
      filter: blur(10px);
      transform: rotate(15deg);
    }

    /* Secondary Stripe 2: Yellow-Green Accent */
    .aurora3 {
      z-index: 2;
      width: 20px;
      height: 260px;
      margin: auto;
      left: 70px;
      right: 0;
      top: 47%;
      /* Gradient shifted to a yellow-green and deep emerald */
      background: linear-gradient(to right, #FCD34D, #059669);
      display: block;
      border-radius: 30px;
      opacity: .4;
      position: absolute;
      filter: blur(15px);
      transform: rotate(16deg);
    }

  }

  /* Ground Shadow: Accent Glow */
  .sombra {
    width: 100px;
    height: 9px;
    left: 0px;
    right: 0;
    margin: auto;
    position: absolute;
    bottom: 40px;
    /* Base shadow color */
    background-color: #065F46;
    display: block;
    /* Glow changed to a sharp, vibrant lime green */
    box-shadow: 0px 7px 90px 30px #1bf690;
    border-radius: 100%;
    opacity: 0.2;
    animation: sombra 2s ease infinite;
  }
}
</style>