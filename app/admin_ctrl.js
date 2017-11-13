angular.module('homeApp').controller('admin_ctrl', function ($scope,$location) {
    console.log("welcome admin");

    $scope.doLogin=function(){
        $location.path("/document_master");
    }
});