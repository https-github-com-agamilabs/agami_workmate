<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- plugins from cdn  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.js" integrity="sha512-ZKNVEa7gi0Dz4Rq9jXcySgcPiK+5f01CqW+ZoKLLKr9VMXuCsw3RjWiv8ZpIOa0hxO79np7Ec8DDWALM0bDOaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js" integrity="sha512-d8F1J2kyiRowBB/8/pAWsqUl0wSEOkG5KATkVV4slfblq9VRQ6MyDZVxWl2tWd+mPhuCbpTB4M7uU/x9FlgQ9Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js" integrity="sha512-9CWGXFSJ+/X0LWzSRCZFsOPhSfm6jbnL+Mpqo0o8Ke2SYr8rCTqb4/wGm+9n13HtDE1NQpAEOrMecDZw4FXQGg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js" integrity="sha512-0QbL0ph8Tc8g5bLhfVzSqxe9GERORsKhIn1IrpxDAgUsbBGz/V7iSav2zzW325XGd1OMLdL4UiqRJj702IeqnQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js" integrity="sha512-IsNh5E3eYy3tr/JiX2Yx4vsCujtkhwl7SLqgnwLNgf04Hrt9BT9SXlLlZlWx+OK4ndzAoALhsMNcCmkggjZB1w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/scrollup/2.4.1/jquery.scrollUp.min.js" integrity="sha512-gAHP1RIzRzolApS3+PI5UkCtoeBpdxBAtxEPsyqvsPN950Q7oD+UT2hafrcFoF04oshCGLqcSgR5dhUthCcjdA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js" integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js" integrity="sha512-Zq2BOxyhvnRFXu0+WE6ojpZLOU2jdnqbrM1hmVdGzyeCa1DgM3X5Q4A/Is9xA1IkbUeDd7755dNNI/PzSf2Pew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/5.0.0/imagesloaded.min.js" integrity="sha512-GNb4Gyn5ZK+XcsnKg8jeo5olNYWqH+j+gMvbG1X5Z9Vz7bv/tQ2spLUuKcXo8T3r4p3IxVt0e/ARNwFftqCt+w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>

/*Credits: 
* https://www.developphp.com/video/JavaScript/Circular-Progress-Loader-Canvas-JavaScript-Programming-Tutorial
*/

(function() {
    var Progress = function( element ) {
        this.context = element.getContext( "2d" );
        this.refElement = element.parentNode;
        this.loaded = 0;
        this.start = 4.72;
        this.width = this.context.canvas.width;
        this.height = this.context.canvas.height;
        this.total = parseInt( this.refElement.dataset.percent, 10 );
        this.timer = null;
        this.diff = 0;
        this.init();	
    };
    Progress.prototype = {
        init: function() {
            var self = this;
            self.timer = setInterval(function() {
                self.run();	
            }, 25);
        },
        run: function() {
            var self = this;
            self.diff = ( ( self.loaded / 100 ) * Math.PI * 2 * 10 ).toFixed( 2 );	
            self.context.clearRect( 0, 0, self.width, self.height );
            self.context.lineWidth = 5;
            self.context.fillStyle = "#000000";
            self.context.strokeStyle = "#006cff";
            self.context.textAlign = "center";
            self.context.fillText( self.loaded + "%", self.width * .5, self.height * .5 + 2, self.width );
            self.context.beginPath();
            self.context.arc( 50, 50, 45, self.start, self.diff / 10 + self.start, false );
            self.context.stroke();
            if( self.loaded >= self.total ) {
                clearInterval( self.timer );
            }
            self.loaded++;
        }
    };
    var CircularSkillBar = function( elements ) {
        this.bars = document.querySelectorAll( elements );
        if( this.bars.length > 0 ) {
            this.init();
        }	
    };
    CircularSkillBar.prototype = {
        init: function() {
            this.tick = 25;
            this.progress();
        },
        progress: function() {
            var self = this;
            var index = 0;
            var firstCanvas = self.bars[0].querySelector( "canvas" );
            var firstProg = new Progress( firstCanvas );
            var timer = setInterval(function() {
                index++;
                var canvas = self.bars[index].querySelector( "canvas" );
                var prog = new Progress( canvas );
                if( index == self.bars.length ) {
                        clearInterval( timer );
                } 
            }, self.tick * 100);
        }
    };
    document.addEventListener( "DOMContentLoaded", function() {
        var circularBars = new CircularSkillBar( ".single-pie-bar" );
    });
})();
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js" integrity="sha512-zMfrMAZYAlNClPKjN+JMuslK/B6sPM09BGvrWlW+cymmPmsUT1xJF3P4kxI3lOh9zypakSgWaTpY6vDJY/3Dig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.sticky/1.0.4/jquery.sticky.min.js" integrity="sha512-QABeEm/oYtKZVyaO8mQQjePTPplrV8qoT7PrwHDJCBLqZl5UmuPi3APEcWwtTNOiH24psax69XPQtEo5dAkGcA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.matchHeight/0.7.2/jquery.matchHeight-min.js" integrity="sha512-/bOVV1DV1AQXcypckRwsR9ThoCj7FqTV2/0Bm79bL3YSyLkVideFLE3MIZkq1u5t28ke1c0n31WYCOrO01dsUg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js" integrity="sha512-NqYds8su6jivy1/WLoW8x1tZMRD7/1ZfhWG/jcRQLOzV1k1rIODCpMgoBnar5QXshKJGV7vi0LXLNXPoFsM5Zg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>