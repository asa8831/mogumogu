<footer>
    - CopyRight mogumogu -
  </footer>
  <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

  <script>
    // 検索した場合の位置固定
  $('form').submit(function(){
    const scroll_top = $(window).scrollTop();
    $('input.st',this).prop('value',scroll_top);
  });

  window.onload = function(){
    $(window).scrollTop(<?php echo @ $_REQUEST['scroll_top'];?>)
  }

  </script>

  <script type="text/javascript" src ="main.js"></script>

</body>


</html>