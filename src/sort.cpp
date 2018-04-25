///////////////////////////////////////////////////////////////////////////////
//  SORT: A Simple, Online and Realtime Tracker
//  
//  This is a C++ reimplementation of the open source tracker in
//  https://github.com/abewley/sort
//  Based on the work of Alex Bewley, alex@dynamicdetection.com, 2016
//
//  Cong Ma, mcximing@sina.cn, 2016
//  
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//  
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//  
//  You should have received a copy of the GNU General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
///////////////////////////////////////////////////////////////////////////////

#include <math.h>
#include <iostream>
#include <fstream>
#include <iomanip> // to format image names using setw() and setfill()
#include <unistd.h>    // to check file existence using POSIX function access(). On Linux include <unistd.h>. On Windows include<io.h>
#include <set>
#include <vector>
#include <iterator>
#include "Hungarian.h"
#include "KalmanTracker.h"
#include "sort.h"
#include "opencv2/video/tracking.hpp"
#include "opencv2/highgui/highgui.hpp"

using namespace std;
using namespace cv;



typedef struct TrackingBox
{
	int frame;
	int id;
	Rect_<float> box;
}TrackingBox;


// Computes IOU between two bounding boxes
double GetIOU(Rect_<float> bb_test, Rect_<float> bb_gt)
{
	float in = (bb_test & bb_gt).area();
	float un = bb_test.area() + bb_gt.area() - in;

	if (un < DBL_EPSILON)
		return 0;

	return (double)(in / un);
}


// global variables for counting
#define CNUM 20
int total_frames = 0;
double total_time = 0.0;
vector<Rect_<float>> predictedBoxes;
vector<vector<double>> iouMatrix;
vector<int> assignment;
set<int> unmatchedDetections;
set<int> unmatchedTrajectories;
set<int> allItems;
set<int> matchedItems;
vector<cv::Point> matchedPairs;
vector<TrackingBox> frameTrackingResult;
unsigned int trkNum = 0;
unsigned int detNum = 0;
int frame_count = 0;
int max_age = 10;//avoid tracking very old trackers
int min_hits = 1;
double iouThreshold = 0.1;//highly asscociate the detection


double cycle_time = 0.0;
int64 start_time = 0;
void TestSORT(string seqName, bool display);
void Predictnextframe(vector<KalmanTracker>& trackers,vector<vector<TrackingBox>>& detFrameData);


vector<KalmanTracker> trackers;
int main()
{ 
	//vector<string> sequences = { "PETS09-S2L1", "TUD-Campus", "TUD-Stadtmitte", "ETH-Bahnhof", "ETH-Sunnyday", "ETH-Pedcross2", "KITTI-13", "KITTI-17", "ADL-Rundle-6", "ADL-Rundle-8", "Venice-2" };
	//for (auto seq : sequences)
	//	TestSORT(seq, false);
	TestSORT("PETS09-S2L1", true);

	// Note: time counted here is of tracking procedure, while the running speed bottleneck is opening and parsing detectionFile.
	cout << "Total Tracking took: " << total_time << " for " << total_frames << " frames or " << ((double)total_frames / (double)total_time) << " FPS" << endl;
	vector<int> frame,x,y,w,h;
	frame.push_back(1);frame.push_back(1);frame.push_back(1);
	x.push_back(649);x.push_back(252);x.push_back(499);
	y.push_back(231);y.push_back(207);y.push_back(156);
	w.push_back(44);w.push_back(35);w.push_back(33);
	h.push_back(86);h.push_back(96);h.push_back(76);
	cout<<frame_count<<endl;
	Predict(frame,x,y,w,h);
	cout<<frame_count<<endl;
	frame.clear();x.clear();y.clear();w.clear();h.clear();
	frame.push_back(1);frame.push_back(2);frame.push_back(3);
	x.push_back(498);x.push_back(633);x.push_back(252);
	y.push_back(153);y.push_back(247);y.push_back(223);
	w.push_back(30);w.push_back(43);w.push_back(44);
	h.push_back(79);h.push_back(76);h.push_back(81);
	//vector<int> ID=Predict(frame,x,y,w,h);
	//cout<<ID[1]<<endl;
	return 0;
}
void helloworld(void)
{
	cout << "helloworld!!!!!!!!!!!!!!!!!!!!!==============================================" << endl;
}
vector<IDV> Predictnextframe(vector<KalmanTracker>& trackers,vector<TrackingBox>& detFrameData)
{
	

		
		predictedBoxes.clear();

		for (auto it = trackers.begin(); it != trackers.end();)
		{
			Rect_<float> pBox = (*it).predict();
			if (pBox.x >= 0 && pBox.y >= 0)
			{
				predictedBoxes.push_back(pBox);
				it++;
			}
			else
			{
				it = trackers.erase(it);
				//cerr << "Box invalid at frame: " << frame_count << endl;
			}
		}

		///////////////////////////////////////
		// 3.2. associate detections to tracked object (both represented as bounding boxes)
		// dets : detFrameData[fi]
		trkNum = predictedBoxes.size();
		detNum = detFrameData.size();
		//cout<<"trknum:"<< trkNum <<endl;
		//cout<<"detnum:"<< detNum <<endl;
		iouMatrix.clear();
		iouMatrix.resize(trkNum, vector<double>(detNum, 0));

		for (unsigned int i = 0; i < trkNum; i++) // compute iou matrix as a distance matrix
		{
			for (unsigned int j = 0; j < detNum; j++)
			{
				// use 1-iou because the hungarian algorithm computes a minimum-cost assignment.
				iouMatrix[i][j] = 1 - GetIOU(predictedBoxes[i], detFrameData[j].box);
			}
		}

		// solve the assignment problem using hungarian algorithm.
		// the resulting assignment is [track(prediction) : detection], with len=preNum
		HungarianAlgorithm HungAlgo;
		assignment.clear();
		HungAlgo.Solve(iouMatrix, assignment);

		// find matches, unmatched_detections and unmatched_predictions
		unmatchedTrajectories.clear();
		unmatchedDetections.clear();
		allItems.clear();
		matchedItems.clear();

		if (detNum > trkNum) //	there are unmatched detections
		{
			for (unsigned int n = 0; n < detNum; n++)
				allItems.insert(n);

			for (unsigned int i = 0; i < trkNum; ++i)
				matchedItems.insert(assignment[i]);

			set_difference(allItems.begin(), allItems.end(),
				matchedItems.begin(), matchedItems.end(),
				insert_iterator<set<int>>(unmatchedDetections, unmatchedDetections.begin()));
		}
		else if (detNum < trkNum) // there are unmatched trajectory/predictions
		{
			for (unsigned int i = 0; i < trkNum; ++i)
				if (assignment[i] == -1) // unassigned label will be set as -1 in the assignment algorithm
					unmatchedTrajectories.insert(i);
		}
		else
			;

		// filter out matched with low IOU
		matchedPairs.clear();
		for (unsigned int i = 0; i < trkNum; ++i)
		{
			if (assignment[i] == -1) // pass over invalid values
				continue;
			if (1 - iouMatrix[i][assignment[i]] < iouThreshold)
			{
				unmatchedTrajectories.insert(i);
				unmatchedDetections.insert(assignment[i]);
			}
			else
				matchedPairs.push_back(cv::Point(i, assignment[i]));
		}

		///////////////////////////////////////
		// 3.3. updating trackers

		// update matched trackers with assigned detections.
		// each prediction is corresponding to a tracker
		int detIdx, trkIdx;
		for (unsigned int i = 0; i < matchedPairs.size(); i++)
		{
			trkIdx = matchedPairs[i].x;
			detIdx = matchedPairs[i].y;
			trackers[trkIdx].update(detFrameData[detIdx].box);
		}

		// create and initialise new trackers for unmatched detections
		for (auto umd : unmatchedDetections)
		{
			KalmanTracker tracker = KalmanTracker(detFrameData[umd].box);
			trackers.push_back(tracker);
			
		}
		cout << "==============================================" << endl;
		// get trackers' output
		frameTrackingResult.clear();
		int i=0;
		for (auto it = trackers.begin(); it != trackers.end();)
		{
			//cout<< "mid"<<(*it).m_id<<endl;
			
			if (((*it).m_time_since_update < 1) &&
				((*it).m_hit_streak >= min_hits || frame_count <= min_hits))
			{
				TrackingBox res;
				res.box = (*it).get_state();
				res.id = (*it).m_id + 1;
				i++;
				res.frame = frame_count;
				frameTrackingResult.push_back(res);
				it++;
			}
			else
				it++;

			// remove dead tracklet
			if (it != trackers.end() && (*it).m_time_since_update > max_age)
				it = trackers.erase(it);
		}

		cycle_time = (double)(getTickCount() - start_time);
		total_time += cycle_time / getTickFrequency();

		vector<IDV> ID;	
		vector<TrackingBox> IDB;	
		for ( int i=0;i<detNum;i++)
		{
			for(auto tb : frameTrackingResult)
			{
				if (GetIOU(tb.box, detFrameData[i].box)>= iouThreshold)
				{
					//cout<<"IOU"<<GetIOU(tb.box, detFrameData[i].box)<<endl;
					tb.frame=i+1;
					int checksame=0;
					//cout<<"ID"<<tb.id<<endl;
					for (auto id: IDB)
					{
						if (id.id==tb.id)//if the later one match the same tracker here
						{
							checksame=1;
							
						}
						else
						{
							checksame=0;
						}
					}
					if(checksame==0)
					{
						IDB.push_back(tb);
						float xdiff=tb.box.x+0.5*tb.box.width-detFrameData[i].box.x-0.5*detFrameData[i].box.width;
						float ydiff=tb.box.y+0.5*tb.box.height-detFrameData[i].box.y-0.5*detFrameData[i].box.height;
						float velocity=sqrt(xdiff*xdiff+ydiff*ydiff);
						IDV idv;
						idv.id=tb.id;
						idv.velocity=velocity;
						
						if(GetIOU(tb.box, detFrameData[i].box)< 0.8)//filter the unrelated detection
						{
							idv.id=-1;
						}
						ID.push_back(idv);
						cout<<"id:"<<idv.id<<",,velocity:"<<idv.velocity<<",,x:"<<detFrameData[i].box.x+0.5*detFrameData[i].box.width<<",,y:"<<detFrameData[i].box.y+0.5*detFrameData[i].box.height<<","<<xdiff<<","<<ydiff<<","<<GetIOU(tb.box, detFrameData[i].box)<<endl;
						
					}
					//cout << tb.frame << "," << tb.id << "," << tb.box.x << "," << tb.box.y << "," << tb.box.width << "," << tb.box.height << ",1,-1,-1,-1" << endl;	
					//cout << detFrameData[i].frame << "," << detFrameData[i].id << "," << detFrameData[i].box.x << "," << detFrameData[i].box.y << "," << detFrameData[i].box.width << "," << detFrameData[i].box.height << ",1,-1,-1,-1" << endl;	
				}
			
				
			}
			
		}
	    return ID;
		
	
}

void TestSORT(string seqName, bool display)
{
	cout << "Processing " << seqName << "..." << endl;

	// 0. randomly generate colors, only for display
	RNG rng(0xFFFFFFFF);
	Scalar_<int> randColor[CNUM];
	for (int i = 0; i < CNUM; i++)
		rng.fill(randColor[i], RNG::UNIFORM, 0, 256);

	string imgPath = "D:/Data/Track/2DMOT2015/train/" + seqName + "/img1/";

	

	// 1. read detection file
	ifstream detectionFile;
	string detFileName = "data/" + seqName + "/det.txt";
	detectionFile.open(detFileName);

	if (!detectionFile.is_open())
	{
		cerr << "Error: can not find file " << detFileName << endl;
		return;
	}

	string detLine;
	istringstream ss;
	vector<TrackingBox> detData;
	char ch;
	float tpx, tpy, tpw, tph;

	while ( getline(detectionFile, detLine) )
	{
		TrackingBox tb;

		ss.str(detLine);
		ss >> tb.frame >> ch >> tb.id >> ch;
		ss >> tpx >> ch >> tpy >> ch >> tpw >> ch >> tph;
		ss.str("");

		tb.box = Rect_<float>(Point_<float>(tpx, tpy), Point_<float>(tpx + tpw, tpy + tph));
		detData.push_back(tb);
	}
	detectionFile.close();

	// 2. group detData by frame
	int maxFrame = 0;
	for (auto tb : detData) // find max frame number
	{
		if (maxFrame < tb.frame)
			maxFrame = tb.frame;
	}

	vector<vector<TrackingBox>> detFrameData;
	vector<TrackingBox> tempVec;
	maxFrame=10;
	for (int fi = 0; fi < maxFrame; fi++)
	{
		for (auto tb : detData)
			if (tb.frame == fi + 1) // frame num starts from 1
				tempVec.push_back(tb);
		detFrameData.push_back(tempVec);
		tempVec.clear();
	}

	// 3. update across frames
	int frame_count = 0;
	int max_age = 1;
	int min_hits = 3;
	double iouThreshold = 0.3;
	vector<KalmanTracker> trackers;
	KalmanTracker::kf_count = 0; // tracking id relies on this, so we have to reset it in each seq.

	// variables used in the for-loop
	

	// prepare result file.
	ofstream resultsFile;
	string resFileName = "output/" + seqName + ".txt";
	resultsFile.open(resFileName);

	if (!resultsFile.is_open())
	{
		cerr << "Error: can not create file " << resFileName << endl;
		return;
	}
    int fi=0;
	if (trackers.size() == 0) // the first frame met
		{
			// initialize kalman trackers using first detections.
			for (unsigned int i = 0; i < detFrameData[fi].size(); i++)
			{
				KalmanTracker trk = KalmanTracker(detFrameData[fi][i].box);
				trackers.push_back(trk);
				cout << 1 << "," << 0 + 1 << "," << trk.predict().x << "," << trk.predict().y << "," << trk.predict().width << "," << trk.predict().height << ",1,-1,-1,-1" << endl;
			}
			// output the first frame detections
			cout << "initialize==============================================" << endl;
			for (unsigned int id = 0; id < detFrameData[fi].size(); id++)
			{
				TrackingBox tb = detFrameData[fi][id];
				cout << tb.frame << "," << id + 1 << "," << tb.box.x << "," << tb.box.y << "," << tb.box.width << "," << tb.box.height << ",1,-1,-1,-1" << endl;
			}
		}

	//////////////////////////////////////////////
	// main loop
	for (int fi = 1; fi < maxFrame; fi++)
	{
		total_frames++;
		frame_count++;
		//cout << frame_count << endl;

		// I used to count running time using clock(), but found it seems to conflict with cv::cvWaitkey(),
		// when they both exists, clock() can not get right result. Now I use cv::getTickCount() instead.
		start_time = getTickCount();
		Predictnextframe(trackers,detFrameData[fi]);

	}

	resultsFile.close();

	if (display)
		destroyAllWindows();
}
vector<IDV> Predict(vector<int>& id, vector<int>& x, vector<int>& y, vector<int>& w, vector<int>& h)
{
	
	
	
	vector<TrackingBox> tempVec;
	int boxnumber=x.size();
	for (int i = 0; i < boxnumber; i++)
	{
		TrackingBox tb;
		tb.frame=id[i];
		tb.box = Rect_<float>(Point_<float>(x[i], y[i]), Point_<float>(x[i] + w[i], y[i] + h[i]));
		tempVec.push_back(tb);
		//cout<<"=="<<x[i]<<","<< y[i]<<","<<tb.box.x<<","<< tb.box.y<<endl;
		
	}

	// 3. update across frames
	//KalmanTracker::kf_count = 0; // tracking id relies on this, so we have to reset it in each seq.

	// variables used in the for-loop
	
	vector<IDV> ID;
    int fi=0;
	if (trackers.size() == 0) // the first frame met
	{
			// initialize kalman trackers using first detections.
			for (unsigned int i = 0; i < tempVec.size(); i++)
			{
				KalmanTracker trk = KalmanTracker(tempVec[i].box);
				trackers.push_back(trk);
				cout << 1 << "," << 0 + 1 << "," << trk.predict().x << "," << trk.predict().y << "," << trk.predict().width << "," << trk.predict().height << ",1,-1,-1,-1" << endl;
				IDV idv;
				idv.id=i;
				idv.velocity=0;
				ID.push_back(idv);
			}
			// output the first frame detections
			cout << "initialize==============================================" << endl;
			for (unsigned int id = 0; id < tempVec.size(); id++)
			{
				TrackingBox tb = tempVec[id];
				cout << tb.frame << "," << id + 1 << "," << tb.box.x << "," << tb.box.y << "," << tb.box.width << "," << tb.box.height << ",1,-1,-1,-1" << endl;
				
			}
			frame_count++;
	}
	else
	{
		frame_count++;
		//cout << frame_count << endl;

		// I used to count running time using clock(), but found it seems to conflict with cv::cvWaitkey(),
		// when they both exists, clock() can not get right result. Now I use cv::getTickCount() instead.
		start_time = getTickCount();
		ID=Predictnextframe(trackers,tempVec);

	}

	return ID;
	
}