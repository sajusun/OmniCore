<!-- Switcher -->
<div class="switcher-wrapper">
    <div class="demo_changer">
        <div class="form_holder sidebar-right1">
            <div class="row">
                <div class="predefined_styles">

                    <div class="swichermainleft">
                        <h4>Navigation Style</h4>
                        <div class="skin-body">
                            <div class="switch_section">
                                <div class="switch-toggle d-flex">
                                    <span class="me-auto">Vertical Menu</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch15" value="vertical" id="myonoffswitch34" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch15') == 'vertical' || old('onoffswitch15') == '' ? 'checked' : '' }}>
                                        <label for="myonoffswitch34" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Horizontal Click Menu</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch15" value="horizontal" id="myonoffswitch35" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch15') == 'horizontal' ? 'checked' : '' }}>
                                        <label for="myonoffswitch35" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Horizontal Hover Menu</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch15" value="horizontal-hover" id="myonoffswitch111" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch15') == 'horizontal-hover' ? 'checked' : '' }}>
                                        <label for="myonoffswitch111" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swichermainleft">
                        <h4>LTR and RTL VERSIONS</h4>
                        <div class="skin-body">
                            <div class="switch_section">
                                <div class="switch-toggle d-flex">
                                    <span class="me-auto">LTR Version</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch7" value="ltr" id="myonoffswitch23" class="onoffswitch2-checkbox" autocomplete="off"  readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch7') == 'ltr' || old('onoffswitch7') == '' ? 'checked' : '' }}>
                                        <label for="myonoffswitch23" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">RTL Version</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch7" value="rtl" id="myonoffswitch24" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch7') == 'rtl' ? 'checked' : '' }}>
                                        <label for="myonoffswitch24" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swichermainleft">
                        <h4>Light Theme Style</h4>
                        <div class="skin-body">
                            <div class="switch_section">
                                <div class="switch-toggle d-flex">
                                    <span class="me-auto">Light Theme</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch1" value="light" id="myonoffswitch1" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch1') == 'light' || old('onoffswitch1') == '' ? 'checked' : '' }}>
                                        <label for="myonoffswitch1" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex">
                                    <span class="me-auto">Light Primary</span>
                                    <div class="">
                                        <input class="wpx-30 h-30 input-color-picker color-primary-light" value="#8FBD56" id="colorID" type="color" data-id="bg-color" data-id1="bg-hover" data-id2="bg-border" data-id7="transparentcolor" name="lightPrimary" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swichermainleft">
                        <h4>Dark Theme Style</h4>
                        <div class="skin-body">
                            <div class="switch_section">
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Dark Theme</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch1" value="dark" id="myonoffswitch2" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch1') == 'dark' ? 'checked' : '' }}>
                                        <label for="myonoffswitch2" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Dark Primary</span>
                                    <div class="">
                                        <input class="wpx-30 h-30 input-dark-color-picker color-primary-dark" value="#8FBD56" id="darkPrimaryColorID" type="color" data-id="bg-color" data-id1="bg-hover" data-id2="bg-border" data-id3="primary" data-id8="transparentcolor" name="darkPrimary" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');">
                                    </div>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Dark Custom Background</span>
                                    <div class="">
                                        <input
                                            class="wpx-30 h-30 input-transparent-color-picker color-bg-transparent" value="#8FBD56" id="transparentBgColorID" type="color" data-id5="body" data-id6="boxed-theme" name="transparentBackground" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');">
                                    </div>
                                </div>
                                <div class="switch-toggle">
                                    <span class="">Background Image</span>
                                    <div class="mt-1">
                                        <a class="bg-img bg-img1" href="javascript:void(0);"><img src="{{ asset('backend/images/media/bg-img1.jpg') }}" alt="Bg-Image" id="bgimage1"></a>
                                        <a class="bg-img bg-img2" href="javascript:void(0);"><img src="{{ asset('backend/images/media/bg-img2.jpg') }}" alt="Bg-Image" id="bgimage2"></a>
                                        <a class="bg-img bg-img3" href="javascript:void(0);"><img src="{{ asset('backend/images/media/bg-img3.jpg') }}" alt="Bg-Image" id="bgimage3"></a>
                                        <a class="bg-img bg-img4" href="javascript:void(0);"><img src="{{ asset('backend/images/media/bg-img4.jpg') }}" alt="Bg-Image" id="bgimage4"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swichermainleft menu-styles">
                        <h4>Menu Styles</h4>
                        <div class="skin-body">
                            <div class="switch_section">
                                <div class="switch-toggle lightMenu d-flex">
                                    <span class="me-auto">Light Menu</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch2" value="light" id="myonoffswitch3" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch2') == 'light' || old('onoffswitch2') == '' ? 'checked' : '' }}>
                                        <label for="myonoffswitch3" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle colorMenu d-flex mt-2">
                                    <span class="me-auto">Color Menu</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch2" value="color" id="myonoffswitch4" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch2') == 'color' ? 'checked' : '' }}>
                                        <label for="myonoffswitch4" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle darkMenu d-flex mt-2">
                                    <span class="me-auto">Dark Menu</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch2" value="dark" id="myonoffswitch5" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch2') == 'dark' ? 'checked' : '' }}>
                                        <label for="myonoffswitch5" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle gradientMenu d-flex mt-2">
                                    <span class="me-auto">Gradient Menu</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch2" value="gradient" id="myonoffswitch19" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch2') == 'gradient' ? 'checked' : '' }}>
                                        <label for="myonoffswitch19" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swichermainleft header-styles">
                        <h4>Header Styles</h4>
                        <div class="skin-body">
                            <div class="switch_section">
                                <div class="switch-toggle lightHeader d-flex">
                                    <span class="me-auto">Light Header</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch3" value="light" id="myonoffswitch6" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch3') == 'light' || old('onoffswitch3') == '' ? 'checked' : '' }}>
                                        <label for="myonoffswitch6" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle  colorHeader d-flex mt-2">
                                    <span class="me-auto">Color Header</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch3" value="color" id="myonoffswitch7" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch3') == 'color' ? 'checked' : '' }}>
                                        <label for="myonoffswitch7" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle darkHeader d-flex mt-2">
                                    <span class="me-auto">Dark Header</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch3" value="dark" id="myonoffswitch8" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch3') == 'dark' ? 'checked' : '' }}>
                                        <label for="myonoffswitch8" class="onoffswitch2-label"></label>
                                    </p>
                                </div>

                                <div class="switch-toggle darkHeader d-flex mt-2">
                                    <span class="me-auto">Gradient Header</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch3" value="gradient" id="myonoffswitch20" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch3') == 'gradient' ? 'checked' : '' }}>
                                        <label for="myonoffswitch20" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swichermainleft">
                        <h4>Layout Width Styles</h4>
                        <div class="skin-body">
                            <div class="switch_section">
                                <div class="switch-toggle d-flex">
                                    <span class="me-auto">Full Width</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch4" value="full" id="myonoffswitch9" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ (old('onoffswitch4') == 'full' || !old('onoffswitch4')) ? 'checked' : '' }}>
                                        <label for="myonoffswitch9" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Boxed</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch4" value="boxed" id="myonoffswitch10" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch4') == 'boxed' ? 'checked' : '' }}>
                                        <label for="myonoffswitch10" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swichermainleft">
                        <h4>Layout Positions</h4>
                        <div class="skin-body">
                            <div class="switch_section">
                                <div class="switch-toggle d-flex">
                                    <span class="me-auto">Fixed</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch5" value="fixed" id="myonoffswitch11" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch5') == 'fixed' || !old('onoffswitch5') ? 'checked' : '' }}>
                                        <label for="myonoffswitch11" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Scrollable</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch5" value="scrollable" id="myonoffswitch12" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch5') == 'scrollable' ? 'checked' : '' }}>
                                        <label for="myonoffswitch12" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swichermainleft leftmenu-styles">
                        <h4>Sidemenu layout Styles</h4>
                        <div class="skin-body">
                            <div class="switch_section">
                                <div class="switch-toggle d-flex">
                                    <span class="me-auto">Default Menu</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch6" value="default-menu" id="myonoffswitch13" class="onoffswitch2-checkbox default-menu" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch6') == 'default-menu' || !old('onoffswitch6') ? 'checked' : '' }}>
                                        <label for="myonoffswitch13" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Icon with Text</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch6" value="icon-with-text" id="myonoffswitch14" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch6') == 'icon-with-text' ? 'checked' : '' }}>
                                        <label for="myonoffswitch14" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Icon Overlay</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch6" value="icon-overlay" id="myonoffswitch15" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch6') == 'icon-overlay' ? 'checked' : '' }}>
                                        <label for="myonoffswitch15" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Closed Sidemenu</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch6" value="closed-menu" id="myonoffswitch16" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch6') == 'closed-menu' ? 'checked' : '' }}>
                                        <label for="myonoffswitch16" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Hover Submenu</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch6" value="hover-submenu" id="myonoffswitch17" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch6') == 'hover-submenu' ? 'checked' : '' }}>
                                        <label for="myonoffswitch17" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                                <div class="switch-toggle d-flex mt-2">
                                    <span class="me-auto">Hover Submenu Style 1</span>
                                    <p class="onoffswitch2">
                                        <input type="radio" name="onoffswitch6" value="hover-submenu1" id="myonoffswitch18" class="onoffswitch2-checkbox" autocomplete="off" readonly onmousedown="this.removeAttribute('readonly');" {{ old('onoffswitch6') == 'hover-submenu1' ? 'checked' : '' }}>
                                        <label for="myonoffswitch18" class="onoffswitch2-label"></label>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swichermainleft">
                        <h4>Reset All Styles</h4>
                        <div class="skin-body">
                            <div class="switch_section my-4">
                                <button class="btn btn-danger btn-block resetCustomStyles" id="resetAll" type="button">Reset All</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Switcher -->