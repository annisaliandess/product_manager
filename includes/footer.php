  </main>

  <footer class="site-footer">
    <div class="container footer-inner">
      <p>&copy; <?= date('Y') ?> Toko Bangunan Makmur. Dibangun dengan Desain Industrial Terbaru.</p>
    </div>
  </footer>

  <script>
  (function(){
    var f = document.getElementById('flash-msg');
    if(f) setTimeout(function(){f.style.opacity='0';f.style.transform='translateY(-10px)';setTimeout(function(){f.remove()},300)},4000);

    var inp = document.getElementById('image-input');
    var prev = document.getElementById('image-preview');
    if(inp && prev){
      inp.addEventListener('change',function(){
        var file = this.files[0];
        if(file){
          var r = new FileReader();
          r.onload = function(e){prev.src=e.target.result;prev.style.display='block'};
          r.readAsDataURL(file);
        } else {
          prev.style.display='none';
        }
      });
    }
  })();
  </script>
</body>
</html>
