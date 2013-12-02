<?php

class GeoConversion
{
   //Distance entre deux points en WGS84
   public function getDistance($latitude1, $longitude1, $latitude2, $longitude2)
   {  
      $earth_radius = 6371;  

      $dLat = deg2rad($latitude2 - $latitude1);  
      $dLon = deg2rad($longitude2 - $longitude1);  

      $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);  
      $c = 2 * asin(sqrt($a));  
      $d = $earth_radius * $c;  

      return $d;  
   }
        
   // Algorithmes de l'IGN


   // Calcul de la latitude isometrique
   // in : 
   // phi -> latitude
   // e -> premiere excentricite de l'ellipsoide
   // 
   // out :
   // latitude isometrique
   
   function IGN_ALG001($phi, $e) // Test unitaire OK
   {
      $terme1 = tan(PI()/4 + $phi/2);
      $eSinPhi = $e*sin($phi);
      $terme2 = (1-$eSinPhi)/(1+$eSinPhi);
      $terme2 = pow($terme2,$e/2);
      
      return log($terme1*$terme2);
   }

   // Calcul de la latitude a partir de la latitude isometrique
   // in :
   // li -> latitude isometrique
   // e -> premiere excentricite de l'ellipsoide
   // 
   // out :
   // latitude

   function IGN_ALG002($li, $e) // Test unitaire OK
   {
      $Epsilon = 1E-11; // Tolerance de convergence
      $ExpLi = exp($li);

      $Phiim1 = 2*atan($ExpLi)-PI()/2;

      $ESinP = $e * sin($Phiim1);
      $Miaou = (1+$ESinP)/(1-$ESinP);
      $Miaou = pow($Miaou, $e/2);
      $Phii = 2*atan(($Miaou)*$ExpLi)-PI()/2;
      
      while(abs($Phii-$Phiim1)>$Epsilon)
      {
         $ESinP = $e * sin($Phii);
         $Miaou = (1+$ESinP)/(1-$ESinP);
         $Miaou = pow($Miaou, $e/2);
         $Phiip1 = 2*atan(($Miaou)*$ExpLi)-PI()/2;
         $Phiim1 = $Phii;
         $Phii = $Phiip1;
      }

      return $Phii;
   }

   // Transformation de coordonnees lambda,phi -> X,Y Lambert
   // in :
   // lambda -> longitude par rapport au meridien origine
   // phi -> latitude
   // n -> exposant de la projection
   // c -> constante de la projection
   // e -> premiere excentricite de l'ellipsoide
   // lambdaC -> longitude de l'origine par rapport au meridien origine
   // Xs,Ys -> coordonnees en projection du pole
   // 
   // out :
   // X,Y -> coordonnees en projection du point
   
   function IGN_ALG003($e, $n, $c, $lambdaC, $Xs, $Ys, $lambda, $Phi) // Test unitaire OK
   {
      $LatitudeIso = $this->IGN_ALG001($Phi,$e);
      
      $SubExpr01 = $c*exp(-$n*$LatitudeIso);
      
      $SubExpr02 = $n*($lambda-$lambdaC);
      
      $Result = new StdClass();
      $Result->X = $Xs+$SubExpr01*sin($SubExpr02);
      $Result->Y = $Ys-$SubExpr01*cos($SubExpr02);
      
      return $Result;
   }
        
   // Transformation de coordonnees X,Y Lambert -> lambda, phi
   // in :
   // X,Y -> coordonnees en projection conique conforme Lambert du point
   // n -> exposant de la projection
   // c -> constante de la projection
   // e -> premiere excentricite de l'ellipsoide
   // lambdaC -> longitude de l'origine par rapport au meridien origine
   // Xs,Ys -> coordonnees en projection du pole
   // 
   // out : 
   // lambda -> longitude par rapport au meridien origine
   // phi -> latitude
   
   function IGN_ALG004($X, $Y, $n, $c, $Xs, $Ys, $LambdaC, $e) // Test unitaire OK
   {
      $R = Hypot($X-$Xs, $Y-$Ys);
      $Gamma = atan(($X-$Xs)/($Ys-$Y));
    
      $Result = new StdClass();
      $Result->Lambda = $LambdaC+$Gamma/$n;
      $L = (-1/$n)*log(abs($R/$c));
      
      $Result->Phi = $this->IGN_ALG002($L,$e);
      
      return $Result;    
   }
        
   // Transformation de coordonnees geographiques ellipsoidale en coordonnees cartesiennes
   // in :
   // lambda -> longitude par rapport au meridien origine
   // phi -> latitude
   // he -> hauteur au dessus de l'ellipsoide
   // a -> demi grand axe de l'ellipsoide
   // e -> premiere excentricite de l'ellipsoide
   // 
   // out :
   // X, Y, Z : coordonnees cartesiennes
   
   function IGN_ALG009($Lambda, $Phi, $he, $a, $e) // Test unitaire OK
   {
      $N = $this->IGN_ALG021($Phi, $a, $e);
      $NHeCosPhi = ($N+$he)*cos($Phi);
      $Result = new StdClass();
      $Result->X = $NHeCosPhi*cos($Lambda);
      $Result->Y = $NHeCosPhi*sin($Lambda);
      $Result->Z = ($N*(1-$e*$e)+$he)*sin($Phi);

      return $Result;
   }
        
   // Calcul de la grande normale de l'ellipsoide
   // in :
   // phi -> latitude
   // a -> demi grand axe de l'ellipsoide
   // e -> premiere excentricite de l'ellipsoide
   // 
   // out :
   // grande normale
   
   function IGN_ALG021($Phi, $a, $e) // Test unitaire OK
   {
      return $a/sqrt(1-pow($e*sin($Phi),2));
   }
        
   // Transformation, pour une ellipsoide donne, des coordonnees cartesiennes cartesiennes d'un point en coordonnees geographiques, par la methode de Heiskanen-Moritz-Boucher
   // in :
   // a -> demi grand axe de l'ellipsoide
   // e -> premiere excentricite de l'ellipsoide
   // X, Y, Z : coordonnees cartesiennes
   // 
   // out :
   // lambda -> longitude par rapport au meridien origine
   // phi -> latitude
   // he -> hauteur au dessus de l'ellipsoide
   
   // Fonction auxiliaire
   function IGN_ALG012_Subex01($e, $phi)
   {
      return sqrt(1-($e*sin($phi))*($e*sin($phi)));
   }

   function IGN_ALG012($a, $e, $X, $Y, $Z) // Test unitaire OK
   {
      $Epsilon = 1E-11;
      $Result = new StdClass();
      $Result->Lambda = atan($Y/$X);
      $R2 = Hypot($X,$Y);
      $R3 = Hypot($R2,$Z);
      $ae2 = $a*$e*$e;

      $Phi0 = atan($Z/($R2*(1-$ae2/$R3)));
      $Phi1 = atan(($Z/$R2)*1/(1-$ae2*cos($Phi0)/($R2*$this->IGN_ALG012_Subex01($e, $Phi0))));

      while(abs($Phi1-$Phi0)>$Epsilon)
      {
         $Phi1 = atan(($Z/$R2)*1/(1-$ae2*cos($Phi0)/($R2*$this->IGN_ALG012_Subex01($e,$Phi0))));
         $Phi0 = $Phi1;
      }
      
      $Phi = $Phi1;
      
      $h = ($R2/cos($Phi))-$a/$this->IGN_ALG012_Subex01($e,$Phi);
      $Result->Phi = $Phi;
      $Result->he = $h;
      
      return $Result;
   }
   
   // Transformation de coordonnees a 7 parametres entre deux systeme geodesique
   // in :
   // T = Tx,Ty,Tz -> Translation (de 1 vers 2)
   // D -> facteur d'echelle (de 1 vers 2)
   // R = Rx,Ry,Rz -> angle de rotation (de 1 vers 2)
   // U = Ux,Uy,Uz -> Coordonnees dans 1
   // 
   // out :
   // V = Vx, Vy, Vz -> Coordonnees dans 2
   
   function IGN_ALG013($T, $D, $R, $U) // Test unitaire OK
   {
      $Dp1 = 1 + $D;
      $Result = new StdClass();
      $Result->X = $T->X+$U->X*$Dp1+$U->Z*$R->Y-$U->Y*$R->Z;
      $Result->Y = $T->Y+$U->Y*$Dp1+$U->X*$R->Z-$U->Z*$R->X;
      $Result->Z = $T->Z+$U->Z*$Dp1+$U->Y*$R->X-$U->X*$R->Y;
      
      return $Result;
   }

   // Transformation de coordonnees a 7 parametres entre deux systeme geodesique
   // in :
   // T = Tx,Ty,Tz -> Translation (de 1 vers 2)
   // D -> facteur d'echelle (de 1 vers 2)
   // R = Rx,Ry,Rz -> angle de rotation (de 1 vers 2)
   // V = Vx, Vy, Vz -> Coordonnees dans 2
   // 
   // out :
   // U = Ux,Uy,Uz -> Coordonnees dans 1
   function IGN_ALG063($T, $D, $R, $V) // Test unitaire OK
   {
      $W->X = $V->X - $T->X;
      $W->Y = $V->Y - $T->Y;
      $W->Z = $V->Z - $T->Z;
      $e = 1 + $D;
      $det = $e*($e*$e + $R->X*$R->X + $R->Y*$R->Y + $R->Z*$R->Z);

      $e2Rx2 = $e*$e + $R->X*$R->X;
      $e2Ry2 = $e*$e + $R->Y*$R->Y;
      $e2Rz2 = $e*$e + $R->Z*$R->Z;
      $RxRy  = $R->X * $R->Y;
      $RyRz  = $R->Y * $R->Z;
      $RxRz  = $R->Z * $R->X;

      $Result->X = $e2Rx2             * $W->X  + ($RxRy + $e*$R->Z)  * $W->Y + ($RxRz - $e*$R->Y)  * $W->Z;
      $Result->Y = ($RxRy - $e*$R->Z) * $W->X  + $e2Ry2              * $W->Y + ($RyRz + $e*$R->X)  * $W->Z;
      $Result->Z = ($RxRz + $e*$R->Y) * $W->X  + ($RyRz - $e*$R->X)  * $W->Y + $e2Rz2              * $W->Z;

      $Result->X /= $det;
      $Result->Y /= $det;
      $Result->Z /= $det;

      return $Result;
   }

   function WGS84_To_Lambert($Longitude, $Latitude)
   {
      //Conversion degre-minute-seconde-orientation -> radian
      $Longitude = $Longitude * pi()/180 ;
      $Latitude = $Latitude * pi()/180 ;

      //Conversion WGS84 géographique -> WGS84 cartésien : ALG0009

      // Ellipsoide WGS84
      $a = 6378137.0;     
      $f = 1/298.257223563;
      $b = $a*(1-$f);
      $e = sqrt(($a*$a - $b*$b)/($a*$a));

      $he = 0.0;

      $WGS84_Cartesian = $this->IGN_ALG009($Longitude, $Latitude, $he, $a, $e);

      //Conversion WGS84 cartésien -> NTF cartésien : ALG063
      $D = 0.00;
      $R->X = 0.00;
      $R->Y = 0.00;
      $R->Z = 0.00;
      $T->X = -168.00;
      $T->Y = -060.00;
      $T->Z =  320;

      $NTF_Cartesian = $this->IGN_ALG063($T, $D, $R, $WGS84_Cartesian);


      // Ellipsoide Clarke 1880
      $a = 6378249.2;     
      $f = 1/293.466021;
      $b = $a*(1-$f);
      $e = sqrt(($a*$a - $b*$b)/($a*$a));

      //Conversion NTF cartésien -> NTF géographique : ALG012
      $NTF_Geo = $this->IGN_ALG012($a, $e, $NTF_Cartesian->X, $NTF_Cartesian->Y, $NTF_Cartesian->Z);

      //Conversion NTF géographique -> Lambert : ALG003

      //Paramètres Lambert 2 étendue
      $n  = 0.7289686274;
      $C  = 11745793.39;

      $Xs = 600000;
      $Ys = 8199695.768;

      $LambdaC = 2.337229167 * pi()/180.00;



      $Lambert = $this->IGN_ALG003($e, $n, $C, $LambdaC, $Xs, $Ys, $NTF_Geo->Lambda, $NTF_Geo->Phi);

      return $Lambert;
   }

   function Lambert_To_WGS84($XLambert, $YLambert)
   {
      /*
      ' |---------------------------------------------------------------------------------------------------------------|
      ' | Const | 1 'Lambert I | 2 'Lambert II | 3 'Lambert III | 4 'Lambert IV | 5 'Lambert II Etendue | 6 'Lambert 93 |
      ' |-------|--------------|---------------|----------------|---------------|-----------------------|---------------|
      ' |    n  | 0.7604059656 |  0.7289686274 |   0.6959127966 | 0.6712679322  |    0.7289686274       |  0.7256077650 |
      ' |-------|--------------|---------------|----------------|---------------|-----------------------|---------------|
      ' |    c  | 11603796.98  |  11745793.39  |   11947992.52  | 12136281.99   |    11745793.39        |  11754255.426 |
      ' |-------|--------------|---------------|----------------|---------------|-----------------------|---------------|
      ' |    Xs |   600000.0   |    600000.0   |   600000.0     |      234.358  |    600000.0           |     700000.0  |
      ' |-------|--------------|---------------|----------------|---------------|-----------------------|---------------|
      ' |    Ys | 5657616.674  |  6199695.768  |   6791905.085  |  7239161.542  |    8199695.768        | 12655612.050  |
      ' |---------------------------------------------------------------------------------------------------------------|
      */


      //Conversion Lambert -> NTF geographique : ALG004

      // Ellipsoide Clarke 1880
      $a = 6378249.2;     
      $f = 1/293.466021;
      $b = $a*(1-$f);
      $e = sqrt(($a*$a - $b*$b)/($a*$a));

      $he = 0.0;

      //Paramètre Lambert 2 Etendu
      $n  = 0.7289686274;
      $c  = 11745793.39;

      $Xs = 600000;
      $Ys = 8199695.768;

      $LambdaC = 2.337229167 * pi()/180.00;
      $R1 = $this->IGN_ALG004($XLambert, $YLambert, $n, $c, $Xs, $Ys, $LambdaC, $e);

      //Conversion NTF géographique -> NTF cartésien : ALG0009        
      $NTF_Cartesian = $this->IGN_ALG009($R1->Lambda, $R1->Phi, $he, $a, $e);

      //Conversion NTF cartésien -> WGS84 cartésien : ALG013
      $D = 0.00;
      $R = new StdClass();
      $R->X = 0.00;
      $R->Y = 0.00;
      $R->Z = 0.00;
      $T = new StdClass();
      $T->X = -168.00;
      $T->Y = -060.00;
      $T->Z =  320;
      $WGS84_Cartesian = $this->IGN_ALG013($T, $D, $R, $NTF_Cartesian);
;
      // Ellipsoide WGS84
      $a = 6378137.0;     
      $f = 1/298.257223563;
      $b = $a*(1-$f);
      $e = sqrt(($a*$a - $b*$b)/($a*$a));

      //Conversion WGS84 cartésien -> WGS84 géographique : ALG012
      $LambdaPhi = $this->IGN_ALG012($a, $e, $WGS84_Cartesian->X, $WGS84_Cartesian->Y, $WGS84_Cartesian->Z);

      //Conversion radian -> degre-minute-seconde-orientation
      $Result = new StdClass();
      $Result->Longitude = $LambdaPhi->Lambda * 180 / PI();
      $Result->Latitude  = $LambdaPhi->Phi    * 180 / PI();

      return $Result;
   }
}