# CMAKE generated file: DO NOT EDIT!
# Generated by "Unix Makefiles" Generator, CMake Version 3.5

# Delete rule output on recipe failure.
.DELETE_ON_ERROR:


#=============================================================================
# Special targets provided by cmake.

# Disable implicit rules so canonical targets will work.
.SUFFIXES:


# Remove some rules from gmake that .SUFFIXES does not remove.
SUFFIXES =

.SUFFIXES: .hpux_make_needs_suffix_list


# Suppress display of executed commands.
$(VERBOSE).SILENT:


# A target that is always out of date.
cmake_force:

.PHONY : cmake_force

#=============================================================================
# Set environment variables for the build.

# The shell in which to execute make rules.
SHELL = /bin/sh

# The CMake executable.
CMAKE_COMMAND = /usr/bin/cmake

# The command to remove a file.
RM = /usr/bin/cmake -E remove -f

# Escaping for special characters.
EQUALS = =

# The top-level source directory on which CMake was run.
CMAKE_SOURCE_DIR = /home/zz/yolo_cpp

# The top-level build directory on which CMake was run.
CMAKE_BINARY_DIR = /home/zz/yolo_cpp/build

# Include any dependencies generated for this target.
include app/darknet++/CMakeFiles/darknet_cpp.dir/depend.make

# Include the progress variables for this target.
include app/darknet++/CMakeFiles/darknet_cpp.dir/progress.make

# Include the compile flags for this target's objects.
include app/darknet++/CMakeFiles/darknet_cpp.dir/flags.make

app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o: app/darknet++/CMakeFiles/darknet_cpp.dir/flags.make
app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o: ../app/darknet++/darknet++.cpp
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green --progress-dir=/home/zz/yolo_cpp/build/CMakeFiles --progress-num=$(CMAKE_PROGRESS_1) "Building CXX object app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o"
	cd /home/zz/yolo_cpp/build/app/darknet++ && /usr/bin/c++   $(CXX_DEFINES) $(CXX_INCLUDES) $(CXX_FLAGS) -o CMakeFiles/darknet_cpp.dir/darknet++.cpp.o -c /home/zz/yolo_cpp/app/darknet++/darknet++.cpp

app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.i: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Preprocessing CXX source to CMakeFiles/darknet_cpp.dir/darknet++.cpp.i"
	cd /home/zz/yolo_cpp/build/app/darknet++ && /usr/bin/c++  $(CXX_DEFINES) $(CXX_INCLUDES) $(CXX_FLAGS) -E /home/zz/yolo_cpp/app/darknet++/darknet++.cpp > CMakeFiles/darknet_cpp.dir/darknet++.cpp.i

app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.s: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Compiling CXX source to assembly CMakeFiles/darknet_cpp.dir/darknet++.cpp.s"
	cd /home/zz/yolo_cpp/build/app/darknet++ && /usr/bin/c++  $(CXX_DEFINES) $(CXX_INCLUDES) $(CXX_FLAGS) -S /home/zz/yolo_cpp/app/darknet++/darknet++.cpp -o CMakeFiles/darknet_cpp.dir/darknet++.cpp.s

app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o.requires:

.PHONY : app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o.requires

app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o.provides: app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o.requires
	$(MAKE) -f app/darknet++/CMakeFiles/darknet_cpp.dir/build.make app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o.provides.build
.PHONY : app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o.provides

app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o.provides.build: app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o


# Object files for target darknet_cpp
darknet_cpp_OBJECTS = \
"CMakeFiles/darknet_cpp.dir/darknet++.cpp.o"

# External object files for target darknet_cpp
darknet_cpp_EXTERNAL_OBJECTS =

../darknet_cpp: app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o
../darknet_cpp: app/darknet++/CMakeFiles/darknet_cpp.dir/build.make
../darknet_cpp: yolo++/libdarknet++.so
../darknet_cpp: /usr/lib/x86_64-linux-gnu/libboost_python.so
../darknet_cpp: /usr/local/cuda-8.0/lib64/libcudart_static.a
../darknet_cpp: /usr/lib/x86_64-linux-gnu/librt.so
../darknet_cpp: /usr/local/cuda-8.0/lib64/libcublas.so
../darknet_cpp: /usr/local/cuda-8.0/lib64/libcurand.so
../darknet_cpp: src/libdarknet_core.so
../darknet_cpp: src/libdarknet_core_cuda.so
../darknet_cpp: /usr/local/lib/libopencv_cudabgsegm.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudaobjdetect.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudastereo.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_dnn.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_ml.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_shape.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_stitching.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudafeatures2d.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_superres.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudacodec.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_videostab.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudaoptflow.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudalegacy.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_calib3d.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudawarping.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_features2d.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_flann.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_highgui.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_objdetect.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_photo.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudaimgproc.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudafilters.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudaarithm.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_video.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_videoio.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_imgcodecs.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_imgproc.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_core.so.3.4.0
../darknet_cpp: /usr/local/lib/libopencv_cudev.so.3.4.0
../darknet_cpp: /usr/local/cuda-8.0/lib64/libcudart_static.a
../darknet_cpp: /usr/local/cuda-8.0/lib64/libcublas.so
../darknet_cpp: /usr/local/cuda-8.0/lib64/libcurand.so
../darknet_cpp: /usr/lib/x86_64-linux-gnu/librt.so
../darknet_cpp: app/darknet++/CMakeFiles/darknet_cpp.dir/link.txt
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green --bold --progress-dir=/home/zz/yolo_cpp/build/CMakeFiles --progress-num=$(CMAKE_PROGRESS_2) "Linking CXX executable ../../../darknet_cpp"
	cd /home/zz/yolo_cpp/build/app/darknet++ && $(CMAKE_COMMAND) -E cmake_link_script CMakeFiles/darknet_cpp.dir/link.txt --verbose=$(VERBOSE)

# Rule to build all files generated by this target.
app/darknet++/CMakeFiles/darknet_cpp.dir/build: ../darknet_cpp

.PHONY : app/darknet++/CMakeFiles/darknet_cpp.dir/build

app/darknet++/CMakeFiles/darknet_cpp.dir/requires: app/darknet++/CMakeFiles/darknet_cpp.dir/darknet++.cpp.o.requires

.PHONY : app/darknet++/CMakeFiles/darknet_cpp.dir/requires

app/darknet++/CMakeFiles/darknet_cpp.dir/clean:
	cd /home/zz/yolo_cpp/build/app/darknet++ && $(CMAKE_COMMAND) -P CMakeFiles/darknet_cpp.dir/cmake_clean.cmake
.PHONY : app/darknet++/CMakeFiles/darknet_cpp.dir/clean

app/darknet++/CMakeFiles/darknet_cpp.dir/depend:
	cd /home/zz/yolo_cpp/build && $(CMAKE_COMMAND) -E cmake_depends "Unix Makefiles" /home/zz/yolo_cpp /home/zz/yolo_cpp/app/darknet++ /home/zz/yolo_cpp/build /home/zz/yolo_cpp/build/app/darknet++ /home/zz/yolo_cpp/build/app/darknet++/CMakeFiles/darknet_cpp.dir/DependInfo.cmake --color=$(COLOR)
.PHONY : app/darknet++/CMakeFiles/darknet_cpp.dir/depend
