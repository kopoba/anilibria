(function () {

    let needUpdate = 0;

    $(document).ready(function () {

        window.addEventListener("hashchange", updateOnHashChange);

        $('#switcher')
            .bootstrapToggle(localStorage.getItem('catalogSort') === '1' ? 'on' : 'off')
            .change(function () {
                getCatalog()
                setPaginationPage()
            });

        getCatalog(getCurrentPage());

        // https://bootsnipp.com/snippets/VgkV
        // $(e.target).removeClass("active");
        $(function () {
            $('.button-checkbox').each(function () {
                // Settings
                //e.preventDefault();
                var $widget = $(this),
                    $button = $widget.find('button'),
                    $checkbox = $widget.find('input:checkbox'),
                    color = $button.data('color'),
                    settings = {
                        on: {
                            icon: 'glyphicon glyphicon-check'
                        },
                        off: {
                            icon: 'glyphicon glyphicon-unchecked'
                        }
                    };

                // Event Handlers
                $button.on('click', function () {
                    $checkbox.prop('checked', !$checkbox.is(':checked'));
                    $checkbox.triggerHandler('change');
                    updateDisplay();

                });
                $checkbox.on('change', function () {
                    updateDisplay();
                    getCatalog(1, true);
                });

                // Actions
                function updateDisplay() {
                    var isChecked = $checkbox.is(':checked');

                    // Set the button's state
                    $button.data('state', (isChecked) ? "on" : "off");

                    // Set the button's icon
                    $button.find('.state-icon')
                        .removeClass()
                        .addClass('state-icon ' + settings[$button.data('state')].icon);

                    // Update the button's color
                    if (isChecked) {
                        $button
                            .removeClass('btn-default')
                            .addClass('btn-' + color + ' active');
                    } else {
                        $button
                            .removeClass('btn-' + color + ' active')
                            .addClass('btn-default');
                    }
                }

                // Initialization
                function init() {

                    updateDisplay();

                    // Inject the icon if applicable
                    if ($button.find('.state-icon').length === 0) {
                        $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
                    }
                }

                init();
            });
        });
    });


    $(document).on('click', '[data-catalog-update]', function (e) {
        getCatalog();
        setPaginationPage();
    });


    function xPagination(a) {

        $("#xpagination").pagination({
            items: a,
            itemsOnPage: 12,
            cssStyle: 'light-theme',
            currentPage: getCurrentPage(),
            onPageClick: function(page) {
                setLocationHashOfPage(page)
            }
        });
    }


    function getCatalog(page = 1, update = true) {

        const sort = $("#switcher").prop("checked") ? 1 : 2;
        const finish = $('#catalogFinish').data('state') === 'on' ? 2 : 1;

        localStorage.setItem('catalogSort', sort);

        let year = '';
        let genre = '';
        let xpage = 'favorites';
        let season = '';

        if (location.pathname.substring(1) !== 'pages/favorites.php' && location.pathname.substring(1) !== 'pages/new.php') {
            year = $.trim($('#catalogYear').val().toString().replace(/,/g, ","));
            genre = $.trim($('#catalogGenre').val().toString().replace(/,/g, ","));
            season = $.trim($('#catalogSeason').val().toString().replace(/,/g, ","));
            xpage = 'catalog';
        }

        let search = {"year": year, "genre": genre, "season": season};

        $.post("/public/catalog.php", {page, xpage, sort, finish, search: JSON.stringify(search)}, function (json) {
            const data = JSON.parse(json);

            if (data.err === 'ok') {

                $('.simpleCatalog tbody').html(data.table);

                if (needUpdate !== data.update || update) {

                    xPagination(data.total);
                    needUpdate = data.update;
                }
            }
        });
    }


    /**
     * Get catalog page number
     *
     * @returns {number}
     */
    function getCurrentPage() {
        return Number(location.hash.split('page-')[1]) || 1;
    }


    /**
     * Set new pagination page
     * Update browser hash
     *
     * @param page
     * @returns {void}
     */
    function setPaginationPage(page = 1) {

        // Select new page in pagination
        $("#xpagination").pagination('selectPage', page);

        // Update hash
        setLocationHashOfPage(getCurrentPage());
    }


    function setLocationHashOfPage(page = 1) {

        // Update hash
        location.hash = location.hash.replace('page-' + getCurrentPage(), 'page-' + page);
    }


    /**
     * Update catalog on hash change
     * Set pagination with new page
     *
     * @returns {void}
     */
    function updateOnHashChange() {

        getCatalog(getCurrentPage(), true);
        setPaginationPage(getCurrentPage());

        $('html, body').animate({scrollTop: $(".contentmenu").offset().top}, 0);

    }

})();