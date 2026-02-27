var Doctum = {
    treeJson: {"tree":{"l":0,"n":"","p":"","c":[{"l":1,"n":"App","p":"App","c":[{"l":2,"n":"Auth","p":"App/Auth","c":[{"l":3,"n":"CustomUserProvider","p":"App/Auth/CustomUserProvider"}]},{"l":2,"n":"Console","p":"App/Console","c":[{"l":3,"n":"Commands","p":"App/Console/Commands","c":[{"l":4,"n":"CleanupOldLabels","p":"App/Console/Commands/CleanupOldLabels"}]},{"l":3,"n":"Kernel","p":"App/Console/Kernel"}]},{"l":2,"n":"Helpers","p":"App/Helpers","c":[{"l":3,"n":"Utils","p":"App/Helpers/Utils"},{"l":3,"n":"Validators","p":"App/Helpers/Validators"}]},{"l":2,"n":"Http","p":"App/Http","c":[{"l":3,"n":"Controllers","p":"App/Http/Controllers","c":[{"l":4,"n":"API","p":"App/Http/Controllers/API","c":[{"l":5,"n":"Auth","p":"App/Http/Controllers/API/Auth","c":[{"l":6,"n":"AuthController","p":"App/Http/Controllers/API/Auth/AuthController"},{"l":6,"n":"PasswordRecoveryController","p":"App/Http/Controllers/API/Auth/PasswordRecoveryController"}]},{"l":5,"n":"BookController","p":"App/Http/Controllers/API/BookController"},{"l":5,"n":"CopyController","p":"App/Http/Controllers/API/CopyController"},{"l":5,"n":"GenreController","p":"App/Http/Controllers/API/GenreController"},{"l":5,"n":"LabelController","p":"App/Http/Controllers/API/LabelController"},{"l":5,"n":"LoanController","p":"App/Http/Controllers/API/LoanController"},{"l":5,"n":"SchoolClassController","p":"App/Http/Controllers/API/SchoolClassController"},{"l":5,"n":"SettingController","p":"App/Http/Controllers/API/SettingController"},{"l":5,"n":"StudentController","p":"App/Http/Controllers/API/StudentController"},{"l":5,"n":"UserController","p":"App/Http/Controllers/API/UserController"}]},{"l":4,"n":"Auth","p":"App/Http/Controllers/Auth","c":[{"l":5,"n":"AuthController","p":"App/Http/Controllers/Auth/AuthController"},{"l":5,"n":"PasswordRecoveryController","p":"App/Http/Controllers/Auth/PasswordRecoveryController"}]},{"l":4,"n":"BookController","p":"App/Http/Controllers/BookController"},{"l":4,"n":"Controller","p":"App/Http/Controllers/Controller"},{"l":4,"n":"CopyController","p":"App/Http/Controllers/CopyController"},{"l":4,"n":"GenreController","p":"App/Http/Controllers/GenreController"},{"l":4,"n":"LabelController","p":"App/Http/Controllers/LabelController"},{"l":4,"n":"LoanController","p":"App/Http/Controllers/LoanController"},{"l":4,"n":"SchoolClassController","p":"App/Http/Controllers/SchoolClassController"},{"l":4,"n":"SettingController","p":"App/Http/Controllers/SettingController"},{"l":4,"n":"StudentController","p":"App/Http/Controllers/StudentController"},{"l":4,"n":"UserController","p":"App/Http/Controllers/UserController"}]},{"l":3,"n":"Middleware","p":"App/Http/Middleware","c":[{"l":4,"n":"EncryptCookies","p":"App/Http/Middleware/EncryptCookies"},{"l":4,"n":"EnsureRecoveryCodeIsValid","p":"App/Http/Middleware/EnsureRecoveryCodeIsValid"},{"l":4,"n":"EnsureRecoveryCodeWasSent","p":"App/Http/Middleware/EnsureRecoveryCodeWasSent"},{"l":4,"n":"EnsureUsersExist","p":"App/Http/Middleware/EnsureUsersExist"},{"l":4,"n":"JwtCookieMiddleware","p":"App/Http/Middleware/JwtCookieMiddleware"},{"l":4,"n":"PreventRegistrationIfUsersExist","p":"App/Http/Middleware/PreventRegistrationIfUsersExist"},{"l":4,"n":"RedirectIfAuthenticated","p":"App/Http/Middleware/RedirectIfAuthenticated"},{"l":4,"n":"RedirectIfCodeAlreadySent","p":"App/Http/Middleware/RedirectIfCodeAlreadySent"}]},{"l":3,"n":"Requests","p":"App/Http/Requests","c":[{"l":4,"n":"Auth","p":"App/Http/Requests/Auth","c":[{"l":5,"n":"LoginRequest","p":"App/Http/Requests/Auth/LoginRequest"},{"l":5,"n":"ResetPasswordRequest","p":"App/Http/Requests/Auth/ResetPasswordRequest"},{"l":5,"n":"SendCodeRequest","p":"App/Http/Requests/Auth/SendCodeRequest"},{"l":5,"n":"ValidateCodeRequest","p":"App/Http/Requests/Auth/ValidateCodeRequest"}]},{"l":4,"n":"Book","p":"App/Http/Requests/Book","c":[{"l":5,"n":"BookStoreRequest","p":"App/Http/Requests/Book/BookStoreRequest"},{"l":5,"n":"BookUpdateRequest","p":"App/Http/Requests/Book/BookUpdateRequest"}]},{"l":4,"n":"Copy","p":"App/Http/Requests/Copy","c":[{"l":5,"n":"CopyAddRequest","p":"App/Http/Requests/Copy/CopyAddRequest"}]},{"l":4,"n":"Genre","p":"App/Http/Requests/Genre","c":[{"l":5,"n":"GenreStoreRequest","p":"App/Http/Requests/Genre/GenreStoreRequest"},{"l":5,"n":"GenreUpdateRequest","p":"App/Http/Requests/Genre/GenreUpdateRequest"}]},{"l":4,"n":"Label","p":"App/Http/Requests/Label","c":[{"l":5,"n":"LabelGenerateRequest","p":"App/Http/Requests/Label/LabelGenerateRequest"}]},{"l":4,"n":"Loan","p":"App/Http/Requests/Loan","c":[{"l":5,"n":"LoanStoreRequest","p":"App/Http/Requests/Loan/LoanStoreRequest"}]},{"l":4,"n":"SchoolClass","p":"App/Http/Requests/SchoolClass","c":[{"l":5,"n":"SchoolClassStoreRequest","p":"App/Http/Requests/SchoolClass/SchoolClassStoreRequest"},{"l":5,"n":"SchoolClassUpdateRequest","p":"App/Http/Requests/SchoolClass/SchoolClassUpdateRequest"}]},{"l":4,"n":"Student","p":"App/Http/Requests/Student","c":[{"l":5,"n":"StudentStoreRequest","p":"App/Http/Requests/Student/StudentStoreRequest"},{"l":5,"n":"StudentUpdateRequest","p":"App/Http/Requests/Student/StudentUpdateRequest"}]},{"l":4,"n":"User","p":"App/Http/Requests/User","c":[{"l":5,"n":"UserDestroyRequest","p":"App/Http/Requests/User/UserDestroyRequest"},{"l":5,"n":"UserStoreRequest","p":"App/Http/Requests/User/UserStoreRequest"},{"l":5,"n":"UserUpdateRequest","p":"App/Http/Requests/User/UserUpdateRequest"}]}]},{"l":3,"n":"Traits","p":"App/Http/Traits","c":[{"l":4,"n":"ErrorLoggerTrait","p":"App/Http/Traits/ErrorLoggerTrait"},{"l":4,"n":"JsonResponseTrait","p":"App/Http/Traits/JsonResponseTrait"}]},{"l":3,"n":"Kernel","p":"App/Http/Kernel"}]},{"l":2,"n":"Jobs","p":"App/Jobs","c":[{"l":3,"n":"Email","p":"App/Jobs/Email","c":[{"l":4,"n":"SendLoanExtendJob","p":"App/Jobs/Email/SendLoanExtendJob"},{"l":4,"n":"SendLoanOverdueJob","p":"App/Jobs/Email/SendLoanOverdueJob"},{"l":4,"n":"SendLoanReceiptJob","p":"App/Jobs/Email/SendLoanReceiptJob"},{"l":4,"n":"SendLoanReminderJob","p":"App/Jobs/Email/SendLoanReminderJob"},{"l":4,"n":"SendRecoveryCodeJob","p":"App/Jobs/Email/SendRecoveryCodeJob"}]},{"l":3,"n":"PrintLoanReceiptJob","p":"App/Jobs/PrintLoanReceiptJob"}]},{"l":2,"n":"Models","p":"App/Models","c":[{"l":3,"n":"View","p":"App/Models/View","c":[{"l":4,"n":"BaseModel","p":"App/Models/View/BaseModel"},{"l":4,"n":"Book","p":"App/Models/View/Book"},{"l":4,"n":"Copy","p":"App/Models/View/Copy"},{"l":4,"n":"Loan","p":"App/Models/View/Loan"},{"l":4,"n":"SchoolClass","p":"App/Models/View/SchoolClass"},{"l":4,"n":"Student","p":"App/Models/View/Student"},{"l":4,"n":"StudentSchoolClass","p":"App/Models/View/StudentSchoolClass"}]},{"l":3,"n":"BaseModel","p":"App/Models/BaseModel"},{"l":3,"n":"Book","p":"App/Models/Book"},{"l":3,"n":"Copy","p":"App/Models/Copy"},{"l":3,"n":"Genre","p":"App/Models/Genre"},{"l":3,"n":"Loan","p":"App/Models/Loan"},{"l":3,"n":"PasswordResetCode","p":"App/Models/PasswordResetCode"},{"l":3,"n":"SchoolClass","p":"App/Models/SchoolClass"},{"l":3,"n":"Setting","p":"App/Models/Setting"},{"l":3,"n":"Student","p":"App/Models/Student"},{"l":3,"n":"StudentSchoolClass","p":"App/Models/StudentSchoolClass"},{"l":3,"n":"User","p":"App/Models/User"}]},{"l":2,"n":"Providers","p":"App/Providers","c":[{"l":3,"n":"AppServiceProvider","p":"App/Providers/AppServiceProvider"},{"l":3,"n":"AuthServiceProvider","p":"App/Providers/AuthServiceProvider"},{"l":3,"n":"EventServiceProvider","p":"App/Providers/EventServiceProvider"},{"l":3,"n":"RouteServiceProvider","p":"App/Providers/RouteServiceProvider"}]},{"l":2,"n":"Rules","p":"App/Rules","c":[{"l":3,"n":"ValidTermInterval","p":"App/Rules/ValidTermInterval"}]},{"l":2,"n":"Services","p":"App/Services","c":[{"l":3,"n":"BookMetadataService","p":"App/Services/BookMetadataService"},{"l":3,"n":"CopyService","p":"App/Services/CopyService"},{"l":3,"n":"EmailService","p":"App/Services/EmailService"},{"l":3,"n":"ThermalPrinterService","p":"App/Services/ThermalPrinterService"}]},{"l":2,"n":"Traits","p":"App/Traits","c":[{"l":3,"n":"HasPaginationSettings","p":"App/Traits/HasPaginationSettings"}]}]},{"l":1,"n":"Database","p":"Database","c":[{"l":2,"n":"Seeders","p":"Database/Seeders","c":[{"l":3,"n":"BooksSeeder","p":"Database/Seeders/BooksSeeder"},{"l":3,"n":"CopiesSeeder","p":"Database/Seeders/CopiesSeeder"},{"l":3,"n":"DatabaseSeeder","p":"Database/Seeders/DatabaseSeeder"},{"l":3,"n":"DatabaseSeederDev","p":"Database/Seeders/DatabaseSeederDev"},{"l":3,"n":"GenresSeeder","p":"Database/Seeders/GenresSeeder"},{"l":3,"n":"LoansSeeder","p":"Database/Seeders/LoansSeeder"},{"l":3,"n":"SchoolClassesSeeder","p":"Database/Seeders/SchoolClassesSeeder"},{"l":3,"n":"SettingsSeeder","p":"Database/Seeders/SettingsSeeder"},{"l":3,"n":"StudentsSeeder","p":"Database/Seeders/StudentsSeeder"}]}]}]},"treeOpenLevel":2},
    /** @var boolean */
    treeLoaded: false,
    /** @var boolean */
    listenersRegistered: false,
    autoCompleteData: null,
    /** @var boolean */
    autoCompleteLoading: false,
    /** @var boolean */
    autoCompleteLoaded: false,
    /** @var string|null */
    rootPath: null,
    /** @var string|null */
    autoCompleteDataUrl: null,
    /** @var HTMLElement|null */
    doctumSearchAutoComplete: null,
    /** @var HTMLElement|null */
    doctumSearchAutoCompleteProgressBarContainer: null,
    /** @var HTMLElement|null */
    doctumSearchAutoCompleteProgressBar: null,
    /** @var number */
    doctumSearchAutoCompleteProgressBarPercent: 0,
    /** @var autoComplete|null */
    autoCompleteJS: null,
    querySearchSecurityRegex: /([^0-9a-zA-Z:\\\\_\s])/gi,
    buildTreeNode: function (treeNode, htmlNode, treeOpenLevel) {
        var ulNode = document.createElement('ul');
        for (var childKey in treeNode.c) {
            var child = treeNode.c[childKey];
            var liClass = document.createElement('li');
            var hasChildren = child.hasOwnProperty('c');
            var nodeSpecialName = (hasChildren ? 'namespace:' : 'class:') + child.p.replace(/\//g, '_');
            liClass.setAttribute('data-name', nodeSpecialName);

            // Create the node that will have the text
            var divHd = document.createElement('div');
            var levelCss = child.l - 1;
            divHd.className = hasChildren ? 'hd' : 'hd leaf';
            divHd.style.paddingLeft = (hasChildren ? (levelCss * 18) : (8 + (levelCss * 18))) + 'px';
            if (hasChildren) {
                if (child.l <= treeOpenLevel) {
                    liClass.className = 'opened';
                }
                var spanIcon = document.createElement('span');
                spanIcon.className = 'icon icon-play';
                divHd.appendChild(spanIcon);
            }
            var aLink = document.createElement('a');

            // Edit the HTML link to work correctly based on the current depth
            aLink.href = Doctum.rootPath + child.p + '.html';
            aLink.innerText = child.n;
            divHd.appendChild(aLink);
            liClass.appendChild(divHd);

            // It has children
            if (hasChildren) {
                var divBd = document.createElement('div');
                divBd.className = 'bd';
                Doctum.buildTreeNode(child, divBd, treeOpenLevel);
                liClass.appendChild(divBd);
            }
            ulNode.appendChild(liClass);
        }
        htmlNode.appendChild(ulNode);
    },
    initListeners: function () {
        if (Doctum.listenersRegistered) {
            // Quick exit, already registered
            return;
        }
                Doctum.listenersRegistered = true;
    },
    loadTree: function () {
        if (Doctum.treeLoaded) {
            // Quick exit, already registered
            return;
        }
        Doctum.rootPath = document.body.getAttribute('data-root-path');
        Doctum.buildTreeNode(Doctum.treeJson.tree, document.getElementById('api-tree'), Doctum.treeJson.treeOpenLevel);

        // Toggle left-nav divs on click
        $('#api-tree .hd span').on('click', function () {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }
        Doctum.treeLoaded = true;
    },
    pagePartiallyLoaded: function (event) {
        Doctum.initListeners();
        Doctum.loadTree();
        Doctum.loadAutoComplete();
    },
    pageFullyLoaded: function (event) {
        // it may not have received DOMContentLoaded event
        Doctum.initListeners();
        Doctum.loadTree();
        Doctum.loadAutoComplete();
        // Fire the event in the search page too
        if (typeof DoctumSearch === 'object') {
            DoctumSearch.pageFullyLoaded();
        }
    },
    loadAutoComplete: function () {
        if (Doctum.autoCompleteLoaded) {
            // Quick exit, already loaded
            return;
        }
        Doctum.autoCompleteDataUrl = document.body.getAttribute('data-search-index-url');
        Doctum.doctumSearchAutoComplete = document.getElementById('doctum-search-auto-complete');
        Doctum.doctumSearchAutoCompleteProgressBarContainer = document.getElementById('search-progress-bar-container');
        Doctum.doctumSearchAutoCompleteProgressBar = document.getElementById('search-progress-bar');
        if (Doctum.doctumSearchAutoComplete !== null) {
            // Wait for it to be loaded
            Doctum.doctumSearchAutoComplete.addEventListener('init', function (_) {
                Doctum.autoCompleteLoaded = true;
                Doctum.doctumSearchAutoComplete.addEventListener('selection', function (event) {
                    // Go to selection page
                    window.location = Doctum.rootPath + event.detail.selection.value.p;
                });
                Doctum.doctumSearchAutoComplete.addEventListener('navigate', function (event) {
                    // Set selection in text box
                    if (typeof event.detail.selection.value === 'object') {
                        Doctum.doctumSearchAutoComplete.value = event.detail.selection.value.n;
                    }
                });
                Doctum.doctumSearchAutoComplete.addEventListener('results', function (event) {
                    Doctum.markProgressFinished();
                });
            });
        }
        // Check if the lib is loaded
        if (typeof autoComplete === 'function') {
            Doctum.bootAutoComplete();
        }
    },
    markInProgress: function () {
            Doctum.doctumSearchAutoCompleteProgressBarContainer.className = 'search-bar';
            Doctum.doctumSearchAutoCompleteProgressBar.className = 'progress-bar indeterminate';
            if (typeof DoctumSearch === 'object' && DoctumSearch.pageFullyLoaded) {
                DoctumSearch.doctumSearchPageAutoCompleteProgressBarContainer.className = 'search-bar';
                DoctumSearch.doctumSearchPageAutoCompleteProgressBar.className = 'progress-bar indeterminate';
            }
    },
    markProgressFinished: function () {
        Doctum.doctumSearchAutoCompleteProgressBarContainer.className = 'search-bar hidden';
        Doctum.doctumSearchAutoCompleteProgressBar.className = 'progress-bar';
        if (typeof DoctumSearch === 'object' && DoctumSearch.pageFullyLoaded) {
            DoctumSearch.doctumSearchPageAutoCompleteProgressBarContainer.className = 'search-bar hidden';
            DoctumSearch.doctumSearchPageAutoCompleteProgressBar.className = 'progress-bar';
        }
    },
    makeProgess: function () {
        Doctum.makeProgressOnProgressBar(
            Doctum.doctumSearchAutoCompleteProgressBarPercent,
            Doctum.doctumSearchAutoCompleteProgressBar
        );
        if (typeof DoctumSearch === 'object' && DoctumSearch.pageFullyLoaded) {
            Doctum.makeProgressOnProgressBar(
                Doctum.doctumSearchAutoCompleteProgressBarPercent,
                DoctumSearch.doctumSearchPageAutoCompleteProgressBar
            );
        }
    },
    loadAutoCompleteData: function (query) {
        return new Promise(function (resolve, reject) {
            if (Doctum.autoCompleteData !== null) {
                resolve(Doctum.autoCompleteData);
                return;
            }
            Doctum.markInProgress();
            function reqListener() {
                Doctum.autoCompleteLoading = false;
                Doctum.autoCompleteData = JSON.parse(this.responseText).items;
                Doctum.markProgressFinished();

                setTimeout(function () {
                    resolve(Doctum.autoCompleteData);
                }, 50);// Let the UI render once before sending the results for processing. This gives time to the progress bar to hide
            }
            function reqError(err) {
                Doctum.autoCompleteLoading = false;
                Doctum.autoCompleteData = null;
                console.error(err);
                reject(err);
            }

            var oReq = new XMLHttpRequest();
            oReq.onload = reqListener;
            oReq.onerror = reqError;
            oReq.onprogress = function (pe) {
                if (pe.lengthComputable) {
                    Doctum.doctumSearchAutoCompleteProgressBarPercent = parseInt(pe.loaded / pe.total * 100, 10);
                    Doctum.makeProgess();
                }
            };
            oReq.onloadend = function (_) {
                Doctum.markProgressFinished();
            };
            oReq.open('get', Doctum.autoCompleteDataUrl, true);
            oReq.send();
        });
    },
    /**
     * Make some progress on a progress bar
     *
     * @param number percentage
     * @param HTMLElement progressBar
     * @return void
     */
    makeProgressOnProgressBar: function(percentage, progressBar) {
        progressBar.className = 'progress-bar';
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute(
            'aria-valuenow', percentage
        );
    },
    searchEngine: function (query, record) {
        if (typeof query !== 'string') {
            return '';
        }
        // replace all (mode = g) spaces and non breaking spaces (\s) by pipes
        // g = global mode to mark also the second word searched
        // i = case insensitive
        // how this function works:
        // First: search if the query has the keywords in sequence
        // Second: replace the keywords by a mark and leave all the text in between non marked
        
        if (record.match(new RegExp('(' + query.replace(/\s/g, ').*(') + ')', 'gi')) === null) {
            return '';// Does not match
        }

        var replacedRecord = record.replace(new RegExp('(' + query.replace(/\s/g, '|') + ')', 'gi'), function (group) {
            return '<mark class="auto-complete-highlight">' + group + '</mark>';
        });

        if (replacedRecord !== record) {
            return replacedRecord;// This should not happen but just in case there was no match done
        }

        return '';
    },
    /**
     * Clean the search query
     *
     * @param string|null query
     * @return string
     */
    cleanSearchQuery: function (query) {
        if (typeof query !== 'string') {
            return '';
        }
        // replace any chars that could lead to injecting code in our regex
        // remove start or end spaces
        // replace backslashes by an escaped version, use case in search: \myRootFunction
        return query.replace(Doctum.querySearchSecurityRegex, '').trim().replace(/\\/g, '\\\\');
    },
    bootAutoComplete: function () {
        Doctum.autoCompleteJS = new autoComplete(
            {
                selector: '#doctum-search-auto-complete',
                searchEngine: function (query, record) {
                    return Doctum.searchEngine(query, record);
                },
                submit: true,
                data: {
                    src: function (q) {
                        Doctum.markInProgress();
                        return Doctum.loadAutoCompleteData(q);
                    },
                    keys: ['n'],// Data 'Object' key to be searched
                    cache: false, // Is not compatible with async fetch of data
                },
                query: (input) => {
                    return Doctum.cleanSearchQuery(input);
                },
                trigger: (query) => {
                    return Doctum.cleanSearchQuery(query).length > 0;
                },
                resultsList: {
                    tag: 'ul',
                    class: 'auto-complete-dropdown-menu',
                    destination: '#auto-complete-results',
                    position: 'afterbegin',
                    maxResults: 500,
                    noResults: false,
                },
                resultItem: {
                    tag: 'li',
                    class: 'auto-complete-result',
                    highlight: 'auto-complete-highlight',
                    selected: 'auto-complete-selected'
                },
            }
        );
    }
};


document.addEventListener('DOMContentLoaded', Doctum.pagePartiallyLoaded, false);
window.addEventListener('load', Doctum.pageFullyLoaded, false);
