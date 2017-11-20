document.querySelectorAll("[gif]").forEach(function(element) {
    element.onclick = function(){
        this.src = this.getAttribute('gif');
    };
});
