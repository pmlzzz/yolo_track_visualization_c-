#include <Python.h>

int
main(int argc, char *argv[])
{
  	    Py_Initialize();
	    PyObject *pModule, *pArg, *pFunc,*pDict;
	    PyRun_SimpleString("import sys");  
            PyRun_SimpleString("sys.path.append('./')"); 
	    pModule = PyImport_ImportModule("ttt");
            if ( !pModule ) {  
            	printf("can't find ttt.py");  
            	getchar();  
            	return -1;  
            }  
	    pDict = PyModule_GetDict(pModule);  
    	    if ( !pDict ) {  
            	return -1;  
    	    }  
	    pFunc = PyDict_GetItemString(pDict,"print_arg");
 if ( !pFunc || !PyCallable_Check(pFunc) ) {  
        printf("can't find function [add]\n");  
        getchar();  
        return -1;  
     }  
	    pArg = Py_BuildValue("(s)", "hello_python");
	    //PyEval_CallObject(pFunc, pArg);
	    
	    Py_Finalize();
  return 0;
}
