<script>
    $(document).ready(function() {
        $('input[name="type_name"]').hide()
        $('body').on('change', 'select[name="type"]', function(e) {
            let val = $(this).val()
            let listFilterByName = ['products', 'product_categories', 'brands', 'filters', 'flash_sales', 'admin_users', 'sb_branches', 'sb_services', 'slides', 'forms', 'countries', 'provinces', 'districts', 'wards', 'posts', 'post_categories', 'pages', 'menus', 'estates', 'estates_categories'];
            if (listFilterByName.includes(val)) {
                $('input[name="type_name"]').show()
            } else {
                $('input[name="type_name"]').hide()
            }
        })
    })
</script>