@php
if($type === 'shops'){
  $path = 'storage/shops/';
}
if($type === 'products'){
  $path = 'storage/products/';
}

@endphp

<div >
 <div class="cover-slide hover-darken inview">
   @if(empty($filename))
    <img class="img-zoom" src="{{ asset('images/no_image.jpg')}}">
   @else
    <img class="img-zoom" src="{{ asset($path . $filename)}}">
   @endif
  </div>
</div>
<style>
  img {
    max-width: 100%;
    vertical-align: bottom;
  }
  
  .cover-slide {
    position: relative;
    overflow: hidden;
  }
  
  .cover-slide::after {
    content: "";
    position: absolute;
    z-index: 2;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #eaebe6;
    opacity: 0;
  }
  
  .cover-slide.inview::after {
    opacity: 1;
    -webkit-animation-name: kf-cover-slide;
            animation-name: kf-cover-slide;
    -webkit-animation-duration: 1.6s;
            animation-duration: 1.6s;
    -webkit-animation-timing-function: ease-in-out;
            animation-timing-function: ease-in-out;
    -webkit-animation-delay: 0s;
            animation-delay: 0s;
    -webkit-animation-iteration-count: 1;
            animation-iteration-count: 1;
    -webkit-animation-direction: normal;
            animation-direction: normal;
    -webkit-animation-fill-mode: forwards;
            animation-fill-mode: forwards;
  }
  
  @-webkit-keyframes kf-cover-slide {
    0% {
      -webkit-transform-origin: left;
              transform-origin: left;
      -webkit-transform: scaleX(0);
              transform: scaleX(0);
    }
    50% {
      -webkit-transform-origin: left;
              transform-origin: left;
      -webkit-transform: scaleX(1);
              transform: scaleX(1);
    }
    50.1% {
      -webkit-transform-origin: right;
              transform-origin: right;
      -webkit-transform: scaleX(1);
              transform: scaleX(1);
    }
    100% {
      -webkit-transform-origin: right;
              transform-origin: right;
      -webkit-transform: scaleX(0);
              transform: scaleX(0);
    }
  }
  
  @keyframes kf-cover-slide {
    0% {
      -webkit-transform-origin: left;
              transform-origin: left;
      -webkit-transform: scaleX(0);
              transform: scaleX(0);
    }
    50% {
      -webkit-transform-origin: left;
              transform-origin: left;
      -webkit-transform: scaleX(1);
              transform: scaleX(1);
    }
    50.1% {
      -webkit-transform-origin: right;
              transform-origin: right;
      -webkit-transform: scaleX(1);
              transform: scaleX(1);
    }
    100% {
      -webkit-transform-origin: right;
              transform-origin: right;
      -webkit-transform: scaleX(0);
              transform: scaleX(0);
    }
  }
  
  .img-zoom, .bg-img-zoom {
    opacity: 0;
  }
  
  .inview .img-zoom, .inview .bg-img-zoom {
    opacity: 1;
    -webkit-transition: -webkit-transform 0.3s ease;
    transition: -webkit-transform 0.3s ease;
    transition: transform 0.3s ease;
    transition: transform 0.3s ease, -webkit-transform 0.3s ease;
    -webkit-animation-name: kf-img-show;
            animation-name: kf-img-show;
    -webkit-animation-duration: 1.6s;
            animation-duration: 1.6s;
    -webkit-animation-timing-function: ease-in-out;
            animation-timing-function: ease-in-out;
    -webkit-animation-delay: 0s;
            animation-delay: 0s;
    -webkit-animation-iteration-count: 1;
            animation-iteration-count: 1;
    -webkit-animation-direction: normal;
            animation-direction: normal;
    -webkit-animation-fill-mode: none;
            animation-fill-mode: none;
  }
  
  .inview .img-zoom:hover, .inview .bg-img-zoom:hover {
    -webkit-transform: scale(1.05);
            transform: scale(1.05);
  }
  
  @-webkit-keyframes kf-img-show {
    0% {
      opacity: 0;
    }
    50% {
      opacity: 0;
    }
    50.1% {
      opacity: 1;
      -webkit-transform: scale(1.5);
              transform: scale(1.5);
    }
    100% {
      opacity: 1;
    }
  }
  
  @keyframes kf-img-show {
    0% {
      opacity: 0;
    }
    50% {
      opacity: 0;
    }
    50.1% {
      opacity: 1;
      -webkit-transform: scale(1.5);
              transform: scale(1.5);
    }
    100% {
      opacity: 1;
    }
  }
  
  .hover-darken::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    -webkit-transition: background-color 0.3s ease;
    transition: background-color 0.3s ease;
    pointer-events: none;
    -webkit-animation-name: kf-img-show;
            animation-name: kf-img-show;
    -webkit-animation-duration: 1.6s;
            animation-duration: 1.6s;
    -webkit-animation-timing-function: ease-in-out;
            animation-timing-function: ease-in-out;
    -webkit-animation-delay: 0s;
            animation-delay: 0s;
    -webkit-animation-iteration-count: 1;
            animation-iteration-count: 1;
    -webkit-animation-direction: normal;
            animation-direction: normal;
    -webkit-animation-fill-mode: none;
            animation-fill-mode: none;
  }
  
  .hover-darken:hover::before {
    background-color: rgba(0, 0, 0, 0.4);
  }
  
  .bg-img-zoom {
    background-image: url(images/image-1.jpg);
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    width: 100%;
  }
  
  .img-bg50 {
    position: relative;
  }
  
  .img-bg50::before {
    display: block;
    content: '';
    padding-top: 50%;
  }
  /*# sourceMappingURL=style.css.map */
</style>