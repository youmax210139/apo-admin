    {{-- jQuery 2.2.0 --}}
    <script src="/assets/js/jquery.min.js"></script>
    {{-- Bootstrap 3.3.6 --}}
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    {{-- AdminLTE App --}}
    <script src="/assets/dist/js/app.js"></script>
    {{-- dataTables --}}
    <script src="/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/assets/plugins/datatables/dataTables.bootstrap.js"></script>
    {{-- Validator --}}
    <script src="/assets/plugins/bootstrapvalidator/bootstrapValidator.js"></script>
    <script src="/assets/plugins/jQuery-slimScroll/jquery.slimscroll.min.js"></script>
    <script src="/assets/js/jquery.cookie.js"></script>
    <script src="/assets/js/bootstrap-notify.min.js"></script>
    <script src="/assets/js/bootstrap-dialog.min.js"></script>
    {{-- toast --}}
     <script src="/assets/plugins/bootoast/bootoast.js"></script>

    <script src="/assets/plugins/jQueryTimer/jquery.timers.js"></script>
    <script src="/assets/dist/js/dialog.js"></script>
    <script src="/assets/dist/js/common.js"></script>
    {{-- Optionally, you can add Slimscroll and FastClick plugins.
         Both of these plugins are recommended to enhance the
         user experience. Slimscroll is required when using the
         fixed layout. --}}
       <script>
           window.addEventListener('click',window.parent.TabsWaiter); //继承全局监听
           
       </script>
    @yield('js')
</body>
</html>