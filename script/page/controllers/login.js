mainApp.controller('LoginController', ['$scope', '$firebaseAuth', 'FIREBASE_URL',
    function($scope, $firebaseAuth, FIREBASE_URL)
    {
        var config = {
            apiKey: "AIzaSyDByddQmGQ9kYp0cjk4UloY1rW9MPDxZGY",
            authDomain: "ripman-5fa4a.firebaseapp.com",
            databaseURL: "https://ripman-5fa4a.firebaseio.com",
            storageBucket: "ripman-5fa4a.appspot.com",
            messagingSenderId: "699159526607"
        };

        firebase.initializeApp(config);


        $scope.login = function(){

            var email       = $scope.user.email;
            var password    = $scope.user.password;

            firebase.auth().signInWithEmailAndPassword(email, password).catch(function(error) {
                // Handle Errors here.
                var errorCode = error.code;
                var errorMessage = error.message;
                // ...
            });
        }

        $scope.login_title = "Velkommen til ID-Kontroll!";
    }
]);