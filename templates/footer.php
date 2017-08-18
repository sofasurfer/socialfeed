        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                // rollover effect
                $('li-').hover(
                    function () {
                        var $media = $(this).find('.content');
                        var height = $media.height();
                        $media.stop().animate({marginTop: -(height - 82)}, 1000);
                    }, function () {
                        var $media = $(this).find('.content');
                        $media.stop().animate({marginTop: '0px'}, 1000);
                    }
                );
            });
        </script>
    </body>
</html>