<footer class="footer" style="height: 45px;padding: 15px calc(24px / 2);">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                CMS Version {{ get_cms_version() }}
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    <span><script>document.write(new Date().getFullYear())</script> © Your App.</span>
                    Design & Develop by Duy Nguyen
                </div>
            </div>
        </div>
    </div>
</footer>

<section class="progress-box"><div class="progress-run"></div></section>
<section id="loading_box"><div id="loading_image"></div></section>
<section class="loading-wrapper" style="position: absolute"></section>