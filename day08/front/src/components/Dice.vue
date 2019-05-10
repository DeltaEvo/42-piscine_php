<template>
    <div class="dice" :class="`up-${value}`">
        <span class="side side-1">1</span>
        <span class="side side-2">2</span>
        <span class="side side-3">3</span>
        <span class="side side-4">4</span>
        <span class="side side-5">5</span>
        <span class="side side-6">6</span>
    </div>
</template>

<script>
export default {
    props: ['value']
}
</script>

<style scopped>
.dice {
    position: relative;
    transform-style: preserve-3d;
    transform-origin: center center 0px;
    width: 200px;
    height: 200px;
    padding: 50px;
    animation: scramble 1s;
    transition: transform 250ms;
}

.up-1 {
    transform: rotateX(-15deg) rotateY(45deg);
}

.up-2 {
    transform: rotateX(-15deg) rotateY(45deg) rotateZ(90deg);
}
  
.up-3 {
    transform: rotateX(75deg) rotateY(180deg) rotateZ(45deg);
}
  
.up-4 {
    transform: rotateX(75deg) rotateY(0deg) rotateZ(45deg);
}
  
.up-5 {
    transform: rotateX(-15deg) rotateY(-45deg) rotateZ(-90deg);
}
  
.up-6 {
    transform: rotateX(165deg) rotateY(45deg) rotateZ(0deg);
}

.side-1 {
  transform: rotateX(90deg) translateZ(50px);
}

.side-2 {
  transform: rotateY(-90deg) translateZ(50px);
}

.side-3 {
  transform: rotateY(180deg) translateZ(50px);
}

.side-4 {
  transform: translateZ(50px);
}

.side-5 {
  transform: rotateY(90deg) translateZ(50px);
}

.side-6 {
  transform: rotateX(-90deg) translateZ(50px);
}

.side {
    position: absolute;
    background: radial-gradient(#ffffff, #dddddd);
    border: solid 6px #ddd;
    display: block;
    width: 100px;
    height: 100px;
    text-align: center;
    line-height: 100px;
    font-size: 60px;
    font-family: Helvetica;
    color: #444;
}

.up-1 .side-1,
.up-2 .side-2,
.up-3 .side-3,
.up-4 .side-4,
.up-5 .side-5,
.up-6 .side-6 {
    animation: sideColor 1250ms;
    border-color: blue;
}

@keyframes scramble {
    13% {
        transform: rotateX(90deg) rotateY(0deg) translateZ(50px);
    }

    26% {
        transform: rotateX(0deg) rotateY(-90deg) translateZ(50px);
    }

    39% {
        transform: rotateX(0deg) rotateY(180deg) translateZ(50px);
    }

    52% {
        transform: rotateX(0deg) rotateY(0deg) translateZ(50px);
    }

    75% {
       transform: rotateX(0deg) rotateY(90deg) translateZ(50px);
    }

    89% {
        transform: rotateX(-90deg) rotateY(0deg) translateZ(50px);
    }
}

@keyframes sideColor {
    0% {
        border-color:#ddd;
    }
    99% {
        border-color:#ddd;
    }
    100% {
        border-color: blue;
    } 
}
</style>
