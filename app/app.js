var app = angular.module('homeApp', ['ngRoute','oc.lazyLoad']);
app.config(['$routeProvider',
    function ($routeProvider) {
        $routeProvider.
            when('/', {
                title: 'home',
                templateUrl: 'views/home.html',
                resolve: {
                    load: ['$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            name: 'home_ctrl',
                            files: [
                                './app/document_ctrl.js',
                                './js/ui-bootstrap-tpls-1.1.2.min.js'
                            ]
                        });
                    }]
                },
                controller: 'home_ctrl'
            })
            .when('/search', {
                title: 'search',
                templateUrl: 'views/home.html',
                resolve: {
                    load: ['$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            name: 'search_ctrl',
                            files: [
                                './app/document_ctrl.js',
                                './js/ui-bootstrap-tpls-1.1.2.min.js'
                            ]
                        });
                    }]
                },
                controller: 'search_ctrl'
            })
            .when('/admin', {
                title: 'admin',
                templateUrl: 'views/admin.html',
                resolve: {
                    load: ['$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            name: 'admin_ctrl',
                            files: [
                                './app/admin_ctrl.js',
                                './js/ui-bootstrap-tpls-1.1.2.min.js'
                            ]
                        });
                    }]
                },
                controller: 'admin_ctrl'
            })
            .when('/document_master', {
                title: 'document_master',
                templateUrl: 'views/document_master.html',
                resolve: {
                    load: ['$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            name: 'document_master_ctrl',
                            files: [
                                './app/document_ctrl.js',
                                './js/ui-bootstrap-tpls-1.1.2.min.js'
                            ]
                        });
                    }]
                },
                controller: 'document_master_ctrl'
            })
            .when('/add_document', {
                title: 'add_document',
                templateUrl: 'views/add_document.html',
                resolve: {
                    load: ['$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            name: 'add_document_ctrl',
                            files: [
                                './app/document_ctrl.js',
                                './js/ui-bootstrap-tpls-1.1.2.min.js',
                                './js/ng-file-upload-shim.min.js',
                                './js/ng-file-upload.min.js'
                            ]
                        });
                    }]
                },
                controller: 'add_document_ctrl'
            })
            .when('/update_document', {
                templateUrl: 'views/update.html',
                resolve: {
                    load: ['$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            name: 'modal-one',
                            files: [
                                './app/document_ctrl.js',
                                './js/ui-bootstrap-tpls-1.1.2.min.js',
                                './js/ng-file-upload-shim.min.js',
                                './js/ng-file-upload.min.js'
                            ]
                        });
                    }]
                },
                controller: 'update_ctrl'
            })
            .when('/delete_document', {
                templateUrl: 'views/delete.html',
                resolve: {
                    load: ['$ocLazyLoad', function($ocLazyLoad) {
                        return $ocLazyLoad.load({
                            name: 'modal-one',
                            files: [
                                './app/document_ctrl.js',
                                './js/ui-bootstrap-tpls-1.1.2.min.js',
                                './js/ng-file-upload-shim.min.js',
                                './js/ng-file-upload.min.js'
                            ]
                        });
                    }]
                },
                controller: 'delete_ctrl'
            })

    }]).run(function ($rootScope, $location, Data) {
    $rootScope.$on("$routeChangeStart", function (event, next, current) {
        $rootScope.authenticated = false;
        //Data.get('session').then(function (results) {
        //    if(results.code!=200){
        //        $location.path("/");
        //    }
        //});
    });
});