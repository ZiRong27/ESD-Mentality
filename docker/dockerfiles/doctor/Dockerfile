FROM python:3
WORKDIR /usr/src/app
COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt
#All elements before the last element are the source files, the last element is the destination path 
#./ is current directory
COPY ["./doctor.py", "./"]
CMD [ "python", "./doctor.py" ]
#COPY ["./patient.py", "./doctor.py", "./appointment.py", "./"]
#CMD [ "python", "./patient.py", "./doctor.py", "./appointment.py" ]

