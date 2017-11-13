var xar;
var cnt;
var parameter;

angular.module('homeApp').controller('document_master_ctrl', function ($scope,Data,$location) {
    console.log("welcome admin in document_master_ctrl");

    $scope.add_new_document=function(){
        $location.path("/add_document");
    }

    $scope.ok = function (count) {
        //console.log("a = " + xar.response[count].document_name);
        Apprise("<h3>"+xar.response[count].document_name+"</h3><br>Author: "+xar.response[count].document_author+"<br>Tags: "+xar.response[count].document_tag);
    };

    $scope.op = function (count) {
        //Apprise(xar.response[count].document_id);
        cnt=count;
        console.log("id = " + xar.response[cnt].document_id);
        $location.path("/update_document");
        };

    $scope.om = function (count) {
        //Apprise(xar.response[count].document_id);
        cnt=count;
        console.log("id = " + xar.response[cnt].document_id);
        $location.path("/delete_document");
    };

    Data.post('viewAllDocument').then(function (results) {
            if (results.code == 200) {
                $scope.document_master=results.response;
                xar = results;
                console.log("Scope = " + results.response[0].document_name);
            }else{
                console.log(results.message);
            }
    });
});
angular.module('homeApp').controller('add_document_ctrl', function ($scope,$location, Upload) {
    console.log("welcome admin in add_document_ctrl");
    $scope.go_back= function () {
        $location.path("/document_master");
    }

    $scope.tags=[
        {
            "tag_name":"Information Security"
        },
        {
            "tag_name":"Computer Networks"
        },
        {
            "tag_name":"ITA"
        },
        {
            "tag_name":"Software Tools"
        },
        {
            "tag_name":"DBMS"
        }
    ]

    $scope.document_master={
        'document_cover_img':'',
        'document':''
    }

    $scope.$watch('document_master.document', function () {
        $scope.document_temp_name= $scope.document_master.document.name;
    });

    $scope.uploadFile= function (files) {
        $scope.document_master.document_cover_img=files;
    }
    $scope.save_details= function (document_master) {
        console.log(document_master);
        var document_detail={
            "document_name":document_master.document_name,
            "document_author":document_master.document_author,
            "document_format":document_master.document_format,
            "document_tag":document_master.document_tag
        }
        Upload.upload({
            url: './api/addDocument',
            fields: {
                'document_detail': JSON.stringify(document_detail),
                'file1':document_master.document,
                'file2':document_master.document_cover_img[0]
            }

        }).success(function (results, status, headers, config) {
            if (results.code == 200) {
                var document_id=results.response.document_id;

                var document_detail={
                    "document_id":document_id
                };
                $location.path("/document_master");
            } else {
                console.log(results.message);
                $scope.error_message=results.message;
            }
        });
    }
});

angular.module('homeApp').controller('update_ctrl', function ($scope,$location, Upload) {
    console.log("welcome admin in update_ctrl");
    $scope.go_back= function () {
        $location.path("/document_master");
    }

    $scope.tags=[
        {
            "tag_name":"Information Security"
        },
        {
            "tag_name":"Computer Networks"
        },
        {
            "tag_name":"ITA"
        },
        {
            "tag_name":"Software Tools"
        },
        {
            "tag_name":"DBMS"
        }
    ]

    $scope.save_details= function (document_master) {
        console.log(document_master);
        var document_detail={
            "document_name":document_master.document_name,
            "document_id":xar.response[cnt].document_id,
            "document_tag":document_master.document_tag
        }
        Upload.upload({
            url: './api/updateDocument',
            fields: {
                'document_detail': JSON.stringify(document_detail)
            }

        }).success(function (results, status, headers, config) {
            if (results.code == 200) {
                var document_id=results.response.document_id;

                var document_detail={
                    "document_id":document_id
                };
                $location.path("/document_master");
            } else {
                console.log(results.message);
                $scope.error_message=results.message;
            }
        });
    }
});

angular.module('homeApp').controller('delete_ctrl', function ($scope,$location, Upload) {
    console.log("welcome admin in delete_ctrl");
        //$location.path("/document_master");
        var document_detail={
            "document_id":xar.response[cnt].document_id
        }
        Upload.upload({
            url: './api/deleteDocument',
            fields: {
                'document_detail': JSON.stringify(document_detail)
            }

        }).success(function (results, status, headers, config) {
            if (results.code == 200) {
                var document_id=results.response.document_id;

                var document_detail={
                    "document_id":document_id
                };
                $location.path("/document_master");
            } else {
                console.log(results.message);
                $scope.error_message=results.message;
            }
        });
    location.href="/prj1/#/document_master";
});

angular.module('homeApp').controller('home_ctrl', function ($scope,Data,$location) {
    console.log('aa');

    $scope.ok = function (count) {
        Apprise("<h3>"+xar.response[count].document_name+"</h3><br>Author: "+xar.response[count].document_author+"<br>Tags: "+xar.response[count].document_tag);
    };

    $scope.omi = function (a) {
        if(a==null){
        }
        else{
        parameter = JSON.stringify({"document_name":a});
        $location.path("/search");
        }
    };

    Data.post('viewAllDocument').then(function (results) {
        if (results.code == 200) {
            $scope.document_master=results.response;
            xar = results;
            console.log("Scope = " + results.response[0].document_name);
        }else{
            console.log(results.message);
        }
    });

});

angular.module('homeApp').controller('search_ctrl', function ($scope,Data,$location) {
    //console.log('aa');

    $scope.ok = function (count) {
        Apprise("<h3>"+xar.response[count].document_name+"</h3><br>Author: "+xar.response[count].document_author+"<br>Tags: "+xar.response[count].document_tag);
    };

    $scope.omi = function (a) {
        if(a==null){
            $location.path("/");
        }
        else{
            parameter = JSON.stringify({"document_name":a});
            $location.path("/search");
        }
    };

    Data.post('searchDocument',parameter).then(function (results) {
        if (results.code == 200) {
            $scope.document_master=results.response;
            console.log("Scope = " + results.response[0].document_name);
        }else{
            console.log(results.message);
        }
    });

});