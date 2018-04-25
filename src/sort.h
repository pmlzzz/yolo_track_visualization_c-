#include <iostream>
#include <fstream>
#include <iomanip> // to format image names using setw() and setfill()
#include <unistd.h>    // to check file existence using POSIX function access(). On Linux include <unistd.h>. On Windows include<io.h>
#include <set>
#include <vector>
#include <iterator>
#include "opencv2/video/tracking.hpp"
#include "opencv2/highgui/highgui.hpp"
using namespace std;
using namespace cv;
typedef struct IDV //ID+velocity
{
	int id;
	float velocity;
}IDV;

void helloworld(void);
vector<IDV> Predict(vector<int>& frame, vector<int>& x, vector<int>& y, vector<int>& w, vector<int>& h);

