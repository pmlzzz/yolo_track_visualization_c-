# yolo_track_visualization_cpp
Detection part:C++ version YOLO (https://pjreddie.com/darknet/yolo/ ,https://github.com/for-aiur/yolo_cpp)

Tracking part: C++ version SORT (https://github.com/abewley/sort, https://github.com/mcximing/sort-cpp)

Visualization part: ./visualization/final.php  based on dragit.js

SETUP:
1. setup the environment of YOLO (https://pjreddie.com/darknet/yolo/)
2. download yolo.weights
3. (compile)
$cd yolo_track_visualization_c-/
$make 
4. output tracking data into file newtrack768x576.csv
$./darknet detector demo cfg/coco.data cfg/yolo.cfg yolo.weights video/test.avi
5.upload tracking data to mysql
6. final.php

ps
============================================================
#zzyolo tracking modified

changed Files in src:image.cpp(control the output) demo.cpp

add(from SORT project):sort.cpp(control the tracking) sort.h hungarian.cpp huangarian.h KalmanTracker.cpp KalmanTracker.h
