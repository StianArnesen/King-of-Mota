

app.controller('MainController', ['$scope', function($scope){
	$scope.title = 'My garden';
  	$scope.promo = 'Promo string';
  
  $scope.products = 
  [
        {
          name: 'The Book of Trees', 
          price: 19, 
          pubdate: new Date('2014', '03', '08'), 
          cover: 'img/the-book-of-trees.jpg' 
        }, 
        { 
          name: 'Program or be Programmed', 
          price: 8, 
          pubdate: new Date('2013', '08', '01'), 
          cover: 'img/program-or-be-programmed.jpg' 
        },
    	 { 
          name: 'The most human human', 
          price: 99, 
          pubdate: new Date('2012', '04', '21'), 
          cover: 'img/program-or-be-programmed.jpg' 
        },
   		 { 
          name: 'The least human human', 
          price: 12, 
          pubdate: new Date('2015', '12', '01'), 
          cover: 'img/program-or-be-programmed.jpg' 
        }
  ]

}]);